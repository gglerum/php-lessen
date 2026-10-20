Wat we tot nu toe hebben behandeld:
`if` statement
```PHP
if($input === 1){
    echo "Je hebt optie 1 gekozen.\n";
}
```

`for` loop
```PHP
$sum = 0;
for($i=0; $i<=10; $i++){
    $sum += $i * $i - $i;
}
echo $sum . "\n";
```

`while` loop
```PHP
$i = 10;

echo "counting down from $i\n";
while(--$i){
    echo $i . "\n";
}
```

`switch`
```PHP
$input = trim(fgets($stdin));

switch(true){
    case is_number($input):
        echo "$input x $input = " . $input * $input;
        break;
    case $input === 'q':
        exit();
    default:
        echo "Ongeldige invoer. Voer een getal in.";
        break;
}
```

`match`
```PHP
$input = 2;

$result = match($input){
    1 => echo "optie 1",
    2 => echo "optie 2",
    3 => echo "optie 3",
    default => echo "geen optie",
};

echo $result;
```
