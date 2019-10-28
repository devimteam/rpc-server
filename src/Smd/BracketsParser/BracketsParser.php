<?php
namespace Devim\Component\RpcServer\Smd\BracketsParser;

class BracketsParser
{
    public $stringToProcess = "";

    public $optionsFromBrackets = [];
    public $optionsFromBracketsByName = [];

    public function __construct(string $stringToProcess)
    {
        $this->stringToProcess = $stringToProcess;
        $this->parseOptionsFromBrackets();
    }

    /**
     * Кодирует последовательности в ковычках и убирает ковычки
     *
     * @param string $stringToProcess
     * @return string
     */
    public static function encodeInQuotes(string $stringToProcess) : string
    {
        $matchesDouble = [];
        $matchesSingle = [];

        preg_match_all('/\"[^\"]+\"/', $stringToProcess, $matchesDouble);
        preg_match_all('/\'[^\']+\'/', $stringToProcess, $matchesSingle);

        $allQuotesMatches = array_merge($matchesDouble[0], $matchesSingle[0]);
        
        foreach ($allQuotesMatches as $match) {
            $replaced = urlencode(substr($match, 1, strlen($match)-2));
            $stringToProcess = str_replace($match, $replaced, $stringToProcess);
        }

        return $stringToProcess;
    }

    /**
     * Вытаскивает строки из квадратных скобок, приводя их к url
     *
     * @return array
     */
    private function getStringsFromBrackets() : array
    {
        $resultStrings = [];
        $matchesBrackets = [];

        preg_match_all('/\[[^\[\]]+\]/', $this->stringToProcess, $matchesBrackets);
        
        foreach($matchesBrackets[0] as $match){
            $resultString = trim(str_replace(['[', ']'], "", $match));
            $resultString = preg_replace('|\s+|', ' ', $resultString );
            $resultString = self::encodeInQuotes($resultString);
            $resultString = str_replace(" ", "&", $resultString);
            $resultStrings[] =  $resultString;
        }
        return $resultStrings;
    }
    
    /**
     * Парсит параметры из скобок и сохраняет их
     * Если указан параметр self то опции дублируются в ассоциативный массив
     *
     * @return void
     */
    private function parseOptionsFromBrackets()
    {
        $stringsFromBrackets = $this->getStringsFromBrackets();

        // $bracketsOptionsArray = [];
        foreach($stringsFromBrackets as $string){
            $options = [];
            parse_str($string, $options);
            $this->optionsFromBrackets[] = $options;
            if(isset($options['self'])){
                $this->optionsFromBracketsByName[$options['self']] = $options;
            }
        }
        
    }

    /**
     * Возвращает оции из первых скобок
     *
     * @return array
     */
    public function getFirst() : array
    {
        return $this->optionsFromBrackets[0] ?? [];
    }

    /**
     * Возвращает опции из скобок по номеру
     * Отсчёт с нуля
     *
     * @param integer $number
     * @return array
     */
    public function getByNumber(int $number) : array
    {
        return $this->optionsFromBrackets[$number] ?? [];
    }
    /**
     * Возвращает опции из скобок по имени в параметре self
     *
     * @param string $optionsName
     * @return array
     */
    public function getbyName(string $optionsName) : array
    {
        return $this->optionsFromBracketsByName[$optionsName] ?? [];
    }
}