## Nummer raadspel in het echt

1. Glenn gaat voor de groep staan. 
1. Glenn vertelt dat zij een raadspel gaan doen en waar er binnen 10 pogingen een getal geraden moet worden
1. Glenn vraagt aan Tygo een getal te noemen tussen 10 en 100
1. Tygo roept 75
1. Glenn bedenkt een te raden getal tussen 1 en 75 en kiest 5.
1. Glenn vraag aan Ischka een getal te raden tussen 1 en 75
1. Ischka roept 75
1. Glenn controleert of 75 gelijk is aan 5
1. Glenn zegt dat dat niet goed is
1. Glenn zet een streep op het bord. Poging 1
1. Glenn vraagt aan Yke Jelle een getal te raden tussen 1 en 75
1. Yke Jelle roept 25
1. Glenn controleert of 25 gelijk is aan 5
1. Glenn zegt dat dat niet goed is
1. Glenn zet een streep op het bord. Poging 2
1. Glenn vraagt aan Jacco een een getal te raden tussen 1 en 75
1. Jacco roept 92
1. Glenn zegt dat het een getal moet zijn tussen 1 en 75
1. Glenn vraagt Jacco nog een poging te doen
1. Jacco roept 12
1. Glenn controleert of 12 gelijk is aan 5
1. Glenn zegt dat dat niet goed is
1. Glenn zet een streep op het bord. Poging 3
1. Glenn vraagt aan Finn een een getal te raden tussen 1 en 75
1. Finn roept "Je moeder"
1. Glenn zegt dat dat geen getal is. Het moet een getal zijn tussen 1 en 75
1. Glenn vraag Finn nog een poging te doen
1. Finn roept 5
1. Glenn controleert of 5 gelijk is aan 5
1. Glenn zegt dat het getal is geraden

## Wat zijn de waarden die bij elk spel variatie kunnen verschillen? Wat is variable?
- Systeem (SYSTEM): Glenn. Kan iemand anders zijn of een computer
- Aantal pogingen (MAX_GUESSES): 10
- Maximaal te raden getal Lower Bound (MAX_LBOUND): 10
- Maximaal te raden getal Upper Bound (MAX_UBOUND): 100
- Maximaal te raden getal (INPUT_MAX): 75
- Minimaal te raden getal (INPUT_MIN): 1
- Te raden getal (NUMBER_TO_GUESS): 5
- Speler (PLAYER): Ischka, Yke Jelle, Jacco, Finn
- Geraden getal (GUESSED_NUMBER): 75, 25, 92, "Je moeder", 5
- Aantal pogingen (ATTEMPTS): 4

### Raadspel na vervangen met variabelen

1. SYSTEM vertelt dat zij een raadspel gaan doen en waar er binnen MAX_GUESSES=10 pogingen een getal geraden moet worden
1. SYSTEM vraagt aan PLAYER een getal te noemen tussen MAX_LBOUND=10 en MAX_UBOUND=100
1. PLAYER roept INPUT_MAX=75
1. SYSTEM bedenkt een te raden getal tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=1
1. SYSTEM vraag aan PLAYER een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=75
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=2
1. SYSTEM vraagt aan PLAYER een getal te raden tussen INPUT_MIN en INPUT_MAX
1. PLAYER roept GUESSED_NUMBER=25
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=3
1. SYSTEM vraagt aan PLAYER een een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=92
1. SYSTEM zegt dat het een getal moet zijn tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM vraagt PLAYER nog een poging te doen
1. PLAYER roept GUESSED_NUMBER=12
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=4
1. SYSTEM vraagt aan PLAYER een een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER="Je moeder"
1. SYSTEM zegt dat dat geen getal is. Het moet een getal zijn tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM vraag PLAYER nog een poging te doen
1. PLAYER roept GUESSED_NUMBER=5
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat het getal is geraden

## Uitzonderingen
**Getal buiten de bounds**

1. PLAYER roept GUESSED_NUMBER=92
1. SYSTEM controleert of GUESSED_NUMBER tussen INPUT_MIN en INPUT_MAX ligt
1. SYSTEM zegt dat het een getal moet zijn tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM vraagt PLAYER nog een poging te doen

**Geen getal**
1. PLAYER roept GUESSED_NUMBER="Je moeder"
1. SYSTEM controleert of GUESSED_NUMBER een getal is
1. SYSTEM zegt dat dat geen getal is. Het moet een getal zijn tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM vraag PLAYER nog een poging te doen

### Raadspel zonder uitzonderingen
1. SYSTEM vertelt dat zij een raadspel gaan doen en waar er binnen MAX_GUESSES=10 pogingen een getal geraden moet worden
1. SYSTEM vraagt aan PLAYER een getal te noemen tussen MAX_LBOUND=10 en MAX_UBOUND=100
1. PLAYER roept INPUT_MAX=75
1. SYSTEM bedenkt een te raden getal tussen INPUT_MIN=1 en INPUT_MAX=75
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=1
1. SYSTEM vraag aan PLAYER een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=75
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=2
1. SYSTEM vraagt aan PLAYER een getal te raden tussen INPUT_MIN en INPUT_MAX
1. PLAYER roept GUESSED_NUMBER=25
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=3
1. SYSTEM vraagt aan PLAYER een een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=12
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=4
1. SYSTEM vraagt aan PLAYER een een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=5
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat het getal is geraden

## Herhalingen herkennen
1. SYSTEM zet een streep op het bord. Poging ATTEMPTS=1
1. SYSTEM vraag aan PLAYER een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
1. PLAYER roept GUESSED_NUMBER=75
1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
1. SYSTEM zegt dat dat niet goed is

### Raadspel met herhaling
1. SYSTEM vertelt dat zij een raadspel gaan doen en waar er binnen MAX_GUESSES=10 pogingen een getal geraden moet worden
2. SYSTEM vraagt aan PLAYER een getal te noemen tussen MAX_LBOUND=10 en MAX_UBOUND=100
3. PLAYER roept INPUT_MAX=75
4. SYSTEM bedenkt een te raden getal tussen INPUT_MIN=1 en INPUT_MAX=75
5. PLAYER raad tot GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
    1. SYSTEM zet een streep op het bord. Poging ATTEMPTS+1
    1. SYSTEM vraag aan PLAYER een getal te raden tussen INPUT_MIN=1 en INPUT_MAX=75
    1. PLAYER roept GUESSED_NUMBER
    1. SYSTEM controleert of GUESSED_NUMBER gelijk is aan NUMBER_TO_GUESS
    1. SYSTEM zegt dat dat niet goed is
9. SYSTEM zegt dat het getal is geraden
