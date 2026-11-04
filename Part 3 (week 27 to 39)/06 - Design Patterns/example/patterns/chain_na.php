<?php

declare(strict_types=1);

// elke schakel in de ketting doet één controle en geeft het verzoek anders door aan de volgende
abstract class LoanCheck
{
    private ?LoanCheck $next = null;

    // geeft de volgende schakel terug, zodat we de ketting kunnen opbouwen
    public function then(LoanCheck $next): LoanCheck
    {
        $this->next = $next;

        return $next;
    }

    public function handle(array $request): string
    {
        $error = $this->check($request);

        if ($error !== null) {
            return $error;
        }

        return $this->next?->handle($request) ?? 'ok';
    }

    // geeft een foutmelding terug, of null als deze controle geslaagd is
    abstract protected function check(array $request): ?string;
}

class MemberExists extends LoanCheck
{
    protected function check(array $request): ?string
    {
        return in_array($request['member'], ['Anna', 'Bram']) ? null : 'Onbekend lid';
    }
}

class UnderLoanLimit extends LoanCheck
{
    protected function check(array $request): ?string
    {
        return $request['loans'] < 2 ? null : 'Maximaal aantal uitleningen bereikt';
    }
}

class BookAvailable extends LoanCheck
{
    protected function check(array $request): ?string
    {
        return in_array($request['title'], ['Misery', 'It']) ? null : 'Boek is niet beschikbaar';
    }
}

// we bouwen de ketting op, de volgorde bepalen we hier
$chain = new MemberExists();
$chain->then(new UnderLoanLimit())->then(new BookAvailable());

echo $chain->handle(['member' => 'Chris', 'title' => 'It', 'loans' => 0]) . "\n";
echo $chain->handle(['member' => 'Anna', 'title' => 'It', 'loans' => 2]) . "\n";
echo $chain->handle(['member' => 'Anna', 'title' => 'Carrie', 'loans' => 0]) . "\n";
echo $chain->handle(['member' => 'Anna', 'title' => 'It', 'loans' => 0]) . "\n";

// voor een zelfscan met een andere volgorde of een extra controle bouwen we een andere ketting
// met dezelfde schakels, zonder een van de controles te veranderen
