## Example
On a questionaire website users must upload filled in templates. These uploaded files will be stored on the server and will be
related to the question the files belong to. For some reason a filled in questionaire was called an "application", don't ask me why, but this ambigious name should give you a hint of the overall quality of the code.

This has been implemented in a bad way [Bad example](example/bad/app). All the mistakes where made in `FileController.php`. But to understand the context and optimizations made later on, the used `Model` and `FormRequest` are supplied.

I have refactored the `FileController` which was a major violation of "Break down complex problems into smaller tasks — every function should have one job." and the Single Responsibility Principle. The new code can be found here: [Better example](example/better/app)

### Improvements
#### Dependency injection
**Application**

The `Application` is one of the most frequent used models in our *cough* application. So it makes sense to [bind](https://laravel.com/docs/12.x/container#simple-bindings)
the model in the Service container when either the `{application}` route parameter is present, or `applicationId` is specified
in the request.

To make this happen the following binding was added in the `AppServiceProvider` class:
```PHP
$this->app->bind(Application::class, function () {
    $applicationId = request('applicationId') ?: request()->route('application');
    return Application::findOrFail($applicationId);
});
```

Now the model will be automatically injected when we type hint `Application` in a controller method or one of our `Service` classes.

**Authenticated User**
Since Laravel 11 we can make use of ["contextual attributes"](https://laravel.com/docs/12.x/container#contextual-attributes) that are automatically resolved by the `Service Container`. One of these attributes is the `Authenticated User`. For a `Controller class`, `Controller method` or other class bound by the `Service Container` we don't have to use the `Auth` facade or `auth()` helper to get
the authenticated user. Which gives a clearer view of which dependencies we are actually using.

**Service classes**
We can inject our own `Service` classes as well. And if we don't need any additional logic, we don't have to add a custom binding. 
If we type hint our `Service` class, which I named `AnswerService` in the `Controller` method the `Service Container` will automatically resolve the constructor parameters by injection dependencies.


**Code**
```PHP
class FileController extends Controller
{
    /**
     * Store files for a specific question in an application.
     *
     * @param StoreFileRequest $request
     * @param User $user
     * @param AnswerService $answerService
     * @return void
     */
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        //...
```

#### Making use of Form Request's
##### Authorization logic
As said before authorization logic can be moved to a Form Request when already used, like in our case the `StoreFileRequest`.

```PHP
 /**
 * Determine if the user is authorized to make this request.
 */
public function authorize(): bool
{
    return $this->user()->can('update', [$this->application, $this->user()->organisation_id]);
}
```

Which looks a whole lot better than always returning `true`. Notice that we use the `Authenticated User` that automatically available
in the `Request` by calling `user()` on the `Request` object (we are in a Form Request that inherits from `Request`). Through our
binding in the Service Container, the application should also be resolved in the request and available to us.

##### prepareForValidation
Form Requests are able to sanitize, or prepare in other ways, the data before the validation logic is run. To simplify the logic for checking the maximum upload size and making it available outside of the request, I made a new attribute using this method.

```PHP
/**
 * Prepare the data for validation.
 */
protected function prepareForValidation(): void
{
    $this->merge([
        'total_file_size' => array_reduce($this->file('files'), fn(int $carry, $file) => $carry + $file->getSize(), 0),
    ]);
}
```
This makes it possible to validate this new `total_file_size` attribute instead of the `files` array.

##### Validation logic
As it happens the rules that where validated within the `FileController` where almost a duplication of the rules that were already
declared in the `StoreFileRequest`. The only new validation logic introduced in the `StoreFileRequest` is the new rule `UploadLimit`
which is separate class that handles that logic. Because we have added the `total_file_size` to our request in the `prepareForValidation` method, we now have an elegant rule:

```PHP
/**
 * Run the validation rule.
 */
public function validate(string $attribute, int $value, Closure $fail): void
{
    //check if uploadlimit has been exceeded
    if (request()->user()->getUploadLimit() < $value + request()->user()->getTotalUploadSize()) {
        $fail('Attachments total size exceeds upload limit.');
    }
}
```

Which is used like so:

```PHP
/**
 * Get the validation rules that apply to the request.
 *
 * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
 */
public function rules(): array
{
    return [
        //...
        'files' => 'required|array',
        'files.*' => 'required|mimes:pdf|min:1|max:5120',
        'total_file_size' => ['required', 'integer', 'min:1', new UploadLimit],
    ];
}
```

#### Moving business logic to a service class
Most of the code in the `FileController` `store` method is concerned with answering a question with the uploaded files. Based on my
knowledge of the rest of the system, I have made a `AnswerService` class that will be responsible for the logic of answering normal
questions and questions with file uploads.

I've split the logic up in three logical methods, one that's publically accessible `answerQuestionWithFiles` and the private methods
`answerQuestion` and `storeFiles`.

#### Small Model optimizations
I have made a couple of small corrections to the `User` model.

The property `$fillable` was used to define a lot of properties, if this list expands faster than the properties that are not `fillable`, it makes sense to use the `$guarded` property for the exceptions instead.

Laravel 11 introduced the `once()` helper, which makes it possible to cache the result of any method for the duration of the request.
Like for example the calculation of the `getTotalUploadSize` method is only needed to be done once.

### Result
```PHP
class FileController extends Controller
{
    /**
     * Store files for a specific question in an application.
     *
     * @param StoreFileRequest $request
     * @param User $user
     * @param AnswerService $answerService
     * @return void
     */
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        $validatedData = $request->validated();

        $answerService->answerQuestionWithFiles(
            $validatedData['questionId'],
            $request->file('files')
        );

        $user->updateUploadSizeTotal($validatedData['total_file_size']);
    }
}
```

All the improvements resulted into a controller class that is small enough to show here. All the logic has been delegated to the 
correct classes:
- Authorization and Validation logic to the `StoreFileRequest` and `UploadLimit` rule.
- Business logic to the `AnswerService`
- Resolving of the `Application` model to the `AppServiceProvider`
