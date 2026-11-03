# Classes

## Waarom gebruiken we functies?

Vorige keer hebben we functies behandeld. Even een korte terugblik op waarom we ze gebruiken:

- **Beter georganiseerde code.** Een functie heeft een naam die beschrijft wat er gebeurt, zoals `isValidScore()`. Je hoeft niet meer alle regels te lezen om te weten wat de code doet.
- **Hergebruik.** Een berekening staat op één plek. Verandert de berekening, dan pas je hem maar op één plek aan.
- **Type safety.** Met types bij de parameters en het return type kun je aangeven welke waarden een functie accepteert en teruggeeft. Geef je het verkeerde type mee, dan krijg je een foutmelding in plaats van een verkeerd resultaat.

```PHP
function add(int $firstNumber, int $secondNumber): int
{
    return $firstNumber + $secondNumber;
}
```

## De beperking van alleen functies

Functies lossen een hoop op, maar ze hebben ook een beperking: de data en de functies die er iets mee doen staan los van elkaar. Laten we dit bekijken met een bankrekening. Het saldo bewaren we in een variabele en we maken functies om geld te storten en op te nemen:

```PHP
$balance = 100;

function deposit(int $balance, int $amount): int
{
    return $balance + $amount;
}

function withdraw(int $balance, int $amount): int
{
    if ($amount > $balance) {
        echo "Onvoldoende saldo\n";
        return $balance;
    }

    return $balance - $amount;
}

$balance = deposit($balance, 50);
$balance = withdraw($balance, 30);
```

Dit werkt, maar er zijn een paar problemen:

1. **De functie weet niet bij welke data hij hoort.** We moeten het saldo steeds meegeven en het resultaat zelf weer opslaan. Vergeet je dat, dan klopt het saldo niet meer.
2. **Niets beschermt de data.** `withdraw()` controleert of er genoeg saldo is, maar niets houdt ons tegen om gewoon `$balance = -500;` te schrijven. De controle wordt dan overgeslagen.
3. **Meer rekeningen betekent meer losse variabelen.** Een tweede rekening betekent `$balanceOfSecondAccount`, een derde rekening weer een nieuwe variabele. We moeten zelf bijhouden welke variabele bij welke rekening hoort.
4. **Het groeit snel.** Voeg een rekeninghouder, een rekeningnummer en een limiet toe en elke functie heeft steeds meer parameters nodig. Het wordt onoverzichtelijk welke functie bij welke data hoort. Dit zag je ook al een beetje in `hangman_v2.php`.

Zie `functions_only.php` voor dit voorbeeld.

## Classes

Een class lost dit op door de data en de functies die bij elkaar horen samen te plaatsen. Je kunt een class zien als een blauwdruk. In de blauwdruk staat beschreven welke data iets heeft (properties) en wat het kan doen (methods). Met die blauwdruk kun je zoveel objecten maken als je wilt.

In Python ken je dit al:

```Python
class BankAccount:
    def __init__(self):
        self.balance = 0

    def deposit(self, amount):
        self.balance = self.balance + amount
```

In PHP ziet het er net iets anders uit:

```PHP
class BankAccount
{
    public int $balance = 0;

    public function deposit(int $amount): void
    {
        $this->balance = $this->balance + $amount;
    }
}
```

Let op de verschillen:
- `class` gevolgd door de naam en `{` en `}` om de class te openen en te sluiten.
- Een **property** is een variabele die bij de class hoort. Zoals je ziet geef je er ook een type aan mee.
- Een **method** is een functie die bij de class hoort. Je schrijft hem op dezelfde manier als een gewone functie, maar dan binnen de class.
- `$this` is in PHP wat `self` in Python is: het verwijst naar het object zelf. Waar je in Python `self.balance` schrijft, schrijf je in PHP `$this->balance`.

Een method hoeft het saldo niet meer als parameter te krijgen. Het saldo is een property van het object, dus de method kan er zelf bij.

### Objecten

De class is de blauwdruk. Een **object** (ook wel een instance of exemplaar) maak je met `new`:

```PHP
$account = new BankAccount();
```

Met `->` gebruik je de properties en methods van het object:

```PHP
$account->deposit(150);
echo $account->balance;
```

Elk object heeft zijn eigen data. Een tweede rekening is dus gewoon een tweede object, we hoeven geen nieuwe variabele met een nieuwe naam voor het saldo te bedenken:

```PHP
$account = new BankAccount();
$secondAccount = new BankAccount();

$account->deposit(150);
$secondAccount->deposit(20);
```

`$account` heeft nu een saldo van 150 en `$secondAccount` van 20. Zie `class_basics.php` voor het volledige voorbeeld.

Zo hebben we de eerste twee problemen van de functies voor een deel opgelost: de data en de functies zijn samengevoegd en we kunnen met meerdere objecten werken. De data is nu alleen nog niet beschermd.

### Constructor

Bij een nieuw object weet je graag zeker dat het meteen in een geldige toestand begint. Hiervoor gebruik je de constructor. Dit is de method `__construct()` die automatisch wordt aangeroepen op het moment dat je `new` gebruikt:

```PHP
class BankAccount
{
    public string $owner;
    public int $balance;

    public function __construct(string $owner, int $balance = 0)
    {
        $this->owner = $owner;
        $this->balance = $balance;
    }
}

$account = new BankAccount("John", 100);
```

De argumenten die je bij `new BankAccount(...)` meegeeft, komen binnen als de parameters van de constructor. In Python is dit `__init__`.

Net als bij gewone functies kun je types gebruiken en een standaardwaarde meegeven, zoals `$balance = 0`. Probeer je een object te maken zonder de verplichte argumenten, of geef je het verkeerde type mee, dan krijg je een foutmelding. Zo is een object nooit half ingevuld en helpen de types ons net als bij functies. Zie `constructor.php`.

### Access modifiers

Het derde probleem was dat niets de data beschermt. In `class_basics.php` kun je nog steeds `$account->balance = -500;` schrijven. Dit lossen we op met **access modifiers**. Hiermee bepaal je wie bij een property of method mag:

- `public`: iedereen kan er bij.
- `private`: alleen code binnen de class zelf kan er bij.

```PHP
class BankAccount
{
    private int $balance;

    public function __construct(int $balance = 0)
    {
        $this->balance = $balance;
    }

    public function withdraw(int $amount): void
    {
        if ($amount > $this->balance) {
            echo "Onvoldoende saldo\n";
            return;
        }

        $this->balance = $this->balance - $amount;
    }

    public function getBalance(): int
    {
        return $this->balance;
    }
}
```

Het saldo is nu `private`. Van buiten de class geeft `$account->balance = -500;` een foutmelding. Het saldo kan alleen nog veranderen via de methods, en die bevatten de controles. We maken dus zelf de regels die bepalen hoe een object gebruikt mag worden.

Omdat het saldo `private` is kunnen we het niet meer rechtstreeks lezen. Daarvoor maken we de method `getBalance()`. Zo'n method noemen we een **getter**.

Dit idee, de data afschermen en alleen via methods toegankelijk maken, heet **encapsulation**. Een goede vuistregel is om properties `private` te maken, tenzij er een reden is om dat niet te doen.

Met `readonly` kun je aangeven dat een property na de constructor niet meer mag veranderen. Handig voor waarden die nooit horen te veranderen, zoals de naam van de rekeninghouder:

```PHP
public readonly string $owner;
```

Zie `access_modifiers.php` voor het volledige voorbeeld.

## Samengevat

| Probleem met alleen functies | Oplossing met classes |
|---|---|
| Data en functies staan los van elkaar | Properties en methods staan samen in een class |
| Je moet alle data steeds meegeven | Een method heeft via `$this` toegang tot de data van het object |
| Meerdere exemplaren betekent meerdere variabelen | Meerdere objecten van dezelfde class |
| Data kan overal worden aangepast | `private` properties, aanpassen kan alleen via methods |
| Een waarde kan ongeldig zijn | Constructor, types en controles in de methods |

## Voorbeeldbestanden

De voorbeelden hieronder bouwen stap voor stap op. Alle voorbeelden gebruiken een bankrekening.

1. [functions_only.php](./example/classes/functions_only.php) laat de bankrekening zien met alleen functies, en wat daar misgaat.
2. [class_basics.php](./example/classes/class_basics.php) zet de bankrekening om naar een class met properties en methods. Je ziet ook hoe je met `new` meerdere objecten maakt.
3. [constructor.php](./example/classes/constructor.php) voegt een constructor en types toe.
4. [access_modifiers.php](./example/classes/access_modifiers.php) laat zien hoe je met `private` en `readonly` de data beschermt.

Je kunt de voorbeelden uitvoeren met `php bestandsnaam.php` vanuit de map `example/classes`.

## Opgaven

De opgaven staan in [classes_opgaven_1.md](./classes_opgaven_1.md).
