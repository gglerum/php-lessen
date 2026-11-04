## Voorbeeld: de FileController

Op een website voor vragenlijsten uploaden gebruikers ingevulde sjablonen. De bestanden worden op de server bewaard en gekoppeld aan de vraag waar ze bij horen. Een ingevulde vragenlijst heet in de code om de een of andere reden een "application". Die verwarrende naam geeft al een idee van de kwaliteit van de rest.

De eerste versie is in [bad](../../example/bad/app) te vinden. Alle fouten zitten in [`FileController.php`](../../example/bad/app/Http/Controllers/FileController.php). Om de context en de verbeteringen te begrijpen staan de gebruikte [`Model`](../../example/bad/app/Models/User.php) en [`FormRequest`](../../example/bad/app/Http/Requests/StoreRequest.php) er ook bij.

De `FileController` is daarna gerefactored. De oude versie overtrad "breek een probleem op in kleinere taken, elke functie heeft één taak" en het Single Responsibility Principle. De nieuwe code staat in [better](../../example/better/app).

De uitleg van de werkwijze staat in [refactoring.md](../refactoring.md). Hieronder staan de verbeteringen één voor één.

## Verbeteringen

### Dependency injection
**Application**

De `Application` is het model dat het vaakst in deze applicatie wordt gebruikt. Daarom loont het om het model in de service container te [binden](https://laravel.com/docs/12.x/container#simple-bindings) voor de route parameter `{application}` of de waarde `applicationId` in het request.

Daarvoor is in de [`AppServiceProvider`](../../example/better/app/Providers/AppServiceProvider.php) deze binding toegevoegd:

```PHP
$this->app->bind(Application::class, function () {
    $applicationId = request('applicationId') ?: request()->route('application');
    return Application::findOrFail($applicationId);
});
```

Wanneer we nu `Application` als type declareren in een controller method of een `Service` class, wordt het model automatisch meegegeven.

**De ingelogde gebruiker**

Sinds Laravel 11 bestaan er ["contextual attributes"](https://laravel.com/docs/12.x/container#contextual-attributes) die de service container zelf invult. Eén daarvan is de ingelogde gebruiker. In een class die door de service container wordt aangemaakt hoef je dus geen `Auth` facade of `auth()` helper meer te gebruiken. Daardoor zie je duidelijker welke dependencies de class gebruikt.

**Service classes**

Onze eigen `Service` classes kunnen op dezelfde manier worden geïnjecteerd. Heeft een service geen extra logica nodig, dan hoeft er geen aparte binding te worden gemaakt. Declareer je de [`AnswerService`](../../example/better/app/Services/AnswerService.php) als type in de controller method, dan lost de service container de parameters van de constructor zelf op.

```PHP
class FileController extends Controller
{
    public function store(StoreFileRequest $request, #[CurrentUser] User $user, AnswerService $answerService)
    {
        //...
```

*[Bekijk de hele FileController](../../example/better/app/Http/Controllers/FileController.php)*

### Form Requests gebruiken
**Autorisatie**

Autorisatie hoort in een Form Request als die er al is, zoals hier de [`StoreFileRequest`](../../example/better/app/Http/Requests/StoreFileRequest.php).

```PHP
public function authorize(): bool
{
    return $this->user()->can('update', [$this->application, $this->user()->organisation_id]);
}
```

Dat is een stuk beter dan een `authorize()` die altijd `true` teruggeeft. Let op dat we de ingelogde gebruiker via `user()` van het `Request` object halen. Door de binding in de service container is ook de `application` beschikbaar in de request.

**prepareForValidation**

Een Form Request kan gegevens opschonen of aanvullen voordat de validatie draait. Om de controle op de maximale uploadgrootte te vereenvoudigen is hier een nieuw attribuut aan toegevoegd.

```PHP
protected function prepareForValidation(): void
{
    $this->merge([
        'total_file_size' => FileSize::fromFiles($this->file('files'))->toBytes()
    ]);
}
```

Daardoor kunnen we `total_file_size` valideren in plaats van de hele `files` array.

**Validatieregels**

De regels die in de `FileController` werden gevalideerd, stonden bijna al in de `StoreFileRequest`. Het is dus ook een voorbeeld van DRY. Nieuw is alleen de regel [`UploadLimit`](../../example/better/app/Rules/UploadLimit.php), een eigen class voor de uploadlimiet.

```PHP
public function validate(string $attribute, int $value, Closure $fail): void
{
    // controleer of de uploadlimiet is overschreden
    if (!request()->user()->canUpload($value)) {
        $fail('Attachments total size exceeds upload limit.');
    }
}
```

Dit wordt zo gebruikt:

```PHP
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

### Value object: FileSize
Een grote verbetering is het [`FileSize`](../../example/better/app/ValueObjects/FileSize.php) value object. In plaats van overal losse getallen voor bestandsgroottes rond te geven, is er nu een object dat weet hoe het met groottes omgaat.

- Het is duidelijk dat het om bytes gaat, en niet om kilobytes of iets anders.
- De berekeningen staan op één plek.
- Methods als `exceedsLimit()` en `add()` maken de code leesbaarder.
- Je kunt niet per ongeluk een grootte meegeven waar iets anders hoort.

```PHP
// duidelijk wat hier gebeurt
$user->updateUploadSizeTotal(FileSize::fromBytes($validatedData['total_file_size']));

// de oude manier met losse getallen
$user->updateUploadSizeTotal($totalUploadedSize);
```

De methods van het [`User`](../../example/better/app/Models/User.php) model werken nu met dit object:

```PHP
// oud: losse getallen
public function getTotalUploadSize(): int
public function updateUploadSizeTotal(int $totalUploadedSize): void
public function getUploadLimit(): int

// nieuw: FileSize objecten
public function getTotalUploadSize(): FileSize
public function updateUploadSizeTotal(FileSize $totalUploadedSize): void
public function getUploadLimit(): FileSize
public function canUpload(int $fileSize): bool
```

Nieuw is `canUpload()`. Die bevat alle controles op de uploadlimiet, die eerst verspreid in de controller stonden. Dit is Tell, Don't Ask: de controller vraagt het `User` model of er geüpload mag worden in plaats van zelf de getallen te vergelijken.

### Business logic naar een service class
De oorspronkelijke `FileController` deed veel te veel: bestanden opslaan, valideren en database records bijhouden. Die logica staat nu in een service laag.

Daarbij is er niet gekozen voor één grote service class. De [`AnswerService`](../../example/better/app/Services/AnswerService.php) zoekt per vraagtype de juiste handler en laat die het werk doen:

- De interface [`AnswerAction`](../../example/better/app/Contracts/AnswerAction.php) bepaalt wat elke handler moet kunnen.
- De abstracte class [`AnswerAction`](../../example/better/app/Actions/Answer/AnswerAction.php) bevat wat alle handlers gemeen hebben.
- [`UploadAction`](../../example/better/app/Actions/Answer/UploadAction.php) handelt vragen af waar bestanden bij horen.
- De lijst met handlers staat in [`config/answer.php`](../../example/better/config/answer.php).

Een nieuw vraagtype (tekst, meerkeuze) betekent een nieuwe action class en een regel in de config, zonder bestaande code aan te passen. Dit is Open/Closed uit les 03. Voor alleen bestanden uploaden is dit meer dan nodig. Het laat zien hoe het eruit ziet als er meer types bij komen, en het sluit aan op design patterns die we in les 06 zien.

De binding in de `AppServiceProvider` bouwt de service met de handlers uit de config:

```PHP
$this->app->singleton(AnswerService::class, function ($app) {
    return new AnswerService(
        collect(
            array_map(fn($handler) => $app->make($handler), config('answer.handlers', []))
        )
    );
});
```

- `singleton` maakt één `AnswerService` voor het hele request.
- De lijst met handlers komt uit de config.
- `$app->make()` bouwt elke handler met al zijn dependencies.

### Kleine verbeteringen in het model
- `$fillable` bevatte veel velden. Groeit die lijst sneller dan het aantal velden dat niet mass assignable is, dan is `$guarded` met alleen de uitzonderingen korter.
- Laravel 11 heeft de `once()` helper, waarmee het resultaat van een method voor de duur van het request wordt onthouden. Zo wordt de berekening in `getTotalUploadSize()` maar één keer gedaan.

## Resultaat

```PHP
class FileController extends Controller
{
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

De controller is klein genoeg om hier te laten zien. Alle logica is naar de juiste plek verhuisd:

- Autorisatie en validatie naar de [`StoreFileRequest`](../../example/better/app/Http/Requests/StoreFileRequest.php) en de [`UploadLimit`](../../example/better/app/Rules/UploadLimit.php) regel.
- Business logic naar de [`AnswerService`](../../example/better/app/Services/AnswerService.php).
- Het ophalen van het `Application` model naar de [`AppServiceProvider`](../../example/better/app/Providers/AppServiceProvider.php).
