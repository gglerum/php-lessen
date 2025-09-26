## Example
On a questionaire website users must upload filled in templates. These uploaded files will be stored on the server and will be
related to the question the files belong to. For some reason a filled in questionaire was called an "application", don't ask me why, but this ambigious name should give you a hint of the overall quality of the code.

This has been implemented in a bad way [Bad example](../../example/bad/app). All the mistakes where made in [`FileController.php`](../../example/bad/app/Http/Controllers/FileController.php). But to understand the context and optimizations made later on, the used [`Model`](../../example/bad/app/Models/User.php) and [`FormRequest`](../../example/bad/app/Http/Requests/StoreRequest.php) are supplied.

I have refactored the `FileController` which was a major violation of "Break down complex problems into smaller tasks — every function should have one job." and the Single Responsibility Principle. The new code can be found here: [Better example](../../example/better/app)

### Improvements
#### Dependency injection
**Application**

The `Application` is one of the most frequent used models in our *cough* application. So it makes sense to [bind](https://laravel.com/docs/12.x/container#simple-bindings)
the model in the Service container when either the `{application}` route parameter is present, or `applicationId` is specified
in the request.

To make this happen the following binding was added in the [`AppServiceProvider`](../../example/better/app/Providers/AppServiceProvider.php) class:
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
If we type hint our `Service` class, which I named [`AnswerService`](../../example/better/app/Services/AnswerService.php) in the `Controller` method the `Service Container` will automatically resolve the constructor parameters by injection dependencies.


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
*[View full FileController](../../example/better/app/Http/Controllers/FileController.php)*

#### Making use of Form Request's
##### Authorization logic
As said before authorization logic can be moved to a Form Request when already used, like in our case the [`StoreFileRequest`](../../example/better/app/Http/Requests/StoreFileRequest.php).

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
        'total_file_size' => FileSize::fromFiles($this->file('files'))->toBytes()
    ]);
}
```
This makes it possible to validate this new `total_file_size` attribute instead of the `files` array.

##### Validation logic
As it happens the rules that where validated within the `FileController` where almost a duplication of the rules that were already
declared in the `StoreFileRequest`. The only new validation logic introduced in the `StoreFileRequest` is the new rule [`UploadLimit`](../../example/better/app/Rules/UploadLimit.php)
which is separate class that handles that logic. Because we have added the `total_file_size` to our request in the `prepareForValidation` method, we now have an elegant rule:

```PHP
/**
 * Run the validation rule.
 */
public function validate(string $attribute, int $value, Closure $fail): void
{
    //check if uploadlimit has been exceeded
    if (!request()->user()->canUpload($value)) {
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

#### Value Object Introduction
One of the biggest improvements is the introduction of a [`FileSize`](../../example/better/app/ValueObjects/FileSize.php) value object. Instead of passing around raw integers for file sizes everywhere, we now have a proper object that knows how to handle file size operations.

This gives us several benefits:
- No more confusion about whether a number represents bytes, kilobytes, or something else
- File size calculations are centralized in one place
- We get methods like `exceedsLimit()` and `add()` that make the code much more readable
- Type safety - you can't accidentally pass a file size where you meant to pass something else

```PHP
// Much clearer what's happening here
$user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));

// vs the old way with raw numbers
$user->updateUploadSizeTotal($totalUploadedSize);
```

#### Polymorphic Action Architecture
Instead of just moving business logic to a simple service class, I've implemented a more sophisticated system that can handle different types of questions.

Here's how it works:
- [`AnswerAction`](../../example/better/app/Contracts/AnswerAction.php) interface defines what every question handler must do
- Abstract [`AnswerAction`](../../example/better/app/Actions/Answer/AnswerAction.php) class provides shared functionality
- [`UploadAction`](../../example/better/app/Actions/Answer/UploadAction.php) handles file upload questions specifically  
- New question types can be added without touching existing code
- Everything is configured in [`config/answer.php`](../../example/better/config/answer.php) so it's easy to extend

This might seem like overkill for just file uploads, but it makes the system much more flexible for the future.

#### Service Container Enhancements
**Advanced Service Binding**

The [`AppServiceProvider`](../../example/better/app/Providers/AppServiceProvider.php) does more than just the simple `Application` binding. It also sets up the `AnswerService` with some pretty neat service container magic:

```PHP
$this->app->singleton(AnswerService::class, function ($app) {
    return new AnswerService(
        collect(
            array_map(fn($handler) => $app->make($handler), config('answer.handlers', []))
        )
    );
});
```

What this does:
- Creates one `AnswerService` instance for the entire request (singleton)
- Loads the list of handlers from the config file
- Uses the service container to build each handler with all its dependencies
- Gives the service a collection of ready-to-use handlers

This way, if you want to add a new question type, you just create the handler class and add it to the config. No need to touch the service itself.

#### Model Method Changes
The [`User`](../../example/better/app/Models/User.php) model methods have been completely rewritten to work with the new `FileSize` value object:

```PHP
// Old way - raw integers everywhere
public function getTotalUploadSize(): int
public function updateUploadSizeTotal(int $totalUploadedSize): void
public function getUploadLimit(): int

// New way - proper FileSize objects
public function getTotalUploadSize(): FileSize
public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
public function getUploadLimit(): FileSize
public function canUpload(int $fileSize): bool  // This one's new
```

The new `canUpload()` method is particularly nice because it encapsulates all the upload limit checking logic that used to be scattered around the controller.

#### Moving business logic to a service class

The original `FileController` was doing way too much - handling files, validating uploads, managing database records, you name it. I've moved all that business logic into a proper service layer.

But instead of just creating one big service class, I've set up a system where different types of questions can be handled by different action classes. The [`AnswerService`](../../example/better/app/Services/AnswerService.php) acts as a coordinator that finds the right handler for each question type.

Right now we only have [`UploadAction`](../../example/better/app/Actions/Answer/UploadAction.php) for file uploads, but if we needed to handle text questions, multiple choice, or whatever else, we'd just add new action classes without touching any existing code.

#### Small Model optimizations
I have made a couple of small corrections to the [`User`](../../example/better/app/Models/User.php) model.

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

        $answerService->answerQuestion(
            Question::find($validatedData['questionId']),
            $request->file('files')
        );

        $user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));
    }
}
```

All the improvements resulted into a controller class that is small enough to show here. All the logic has been delegated to the 
correct classes:
- Authorization and Validation logic to the [`StoreFileRequest`](../../example/better/app/Http/Requests/StoreFileRequest.php) and [`UploadLimit`](../../example/better/app/Rules/UploadLimit.php) rule.
- Business logic to the [`AnswerService`](../../example/better/app/Services/AnswerService.php)
- Resolving of the `Application` model to the [`AppServiceProvider`](../../example/better/app/Providers/AppServiceProvider.php)
