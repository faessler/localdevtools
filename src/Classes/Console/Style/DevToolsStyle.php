<?php
namespace Glowpointzero\LocalDevTools\Console\Style;

use \Glowpointzero\LocalDevTools\Utility\StringUtility;

class DevToolsStyle extends \Symfony\Component\Console\Style\SymfonyStyle
{
    protected $lineWidth = 80;
    protected $linePadding = '    ';
    
    const SAY_STYLE_DEFAULT = 'fg=white;bg=black';
    const SAY_STYLE_TITLE = 'fg=black;bg=cyan';
    const SAY_STYLE_SECTION = 'fg=black;bg=white';
    const SAY_STYLE_OK = 'fg=green;bg=black';
    const SAY_STYLE_SUCCESS = 'fg=black;bg=green';
    const SAY_STYLE_WARNING = 'fg=black;bg=yellow';
    const SAY_STYLE_ERROR = 'fg=black;bg=red';
    
    
    public function title($message)
    {
        $this->say(PHP_EOL . PHP_EOL . $message . PHP_EOL . PHP_EOL, self::SAY_STYLE_TITLE);
    }
    
    public function section($message)
    {
        $this->say(PHP_EOL . $message . PHP_EOL, self::SAY_STYLE_SECTION);
    }
    
    public function error($message)
    {
        $this->say(PHP_EOL . $message . PHP_EOL, self::SAY_STYLE_ERROR);
    }
    
    public function warning($message)
    {
        $this->say(PHP_EOL . $message . PHP_EOL, self::SAY_STYLE_WARNING);
    }
    
    public function caution($message)
    {
        $this->say(PHP_EOL . $message . PHP_EOL, self::SAY_STYLE_WARNING);
    }
    
    /**
     * Tell the user a process has started.
     *
     * @param string $message
     */
    public function processingStart($message)
    {
        $lastCharacter = substr($message, -1);
        if ((!in_array($lastCharacter, ['.', '!', ' ', '?']))) {
            $message .= '... ';
        }
        $this->say($this->linePadding . $message, self::SAY_STYLE_DEFAULT, true, false);
    }
    
    /**
     * Tell the user a previously started process has ended successfully.
     *
     * @param string $message
     */
    public function processingEnd($message)
    {
        $this->say($message, self::SAY_STYLE_OK, true, false);
        $this->newLine();
    }
    
    /**
     * Say something to the user in different alert levels (styles),
     * inline or 'en bloc'. This is the raw(er) command for
     * warning() error(), etc.
     *
     * @param type $message
     * @param type $style
     * @param type $inline
     * @param type $addMargins
     */
    public function say($message, $style = self::SAY_STYLE_DEFAULT, $inline = false, $addMargins = true)
    {
        if ($style === null) {
            $style = self::SAY_STYLE_DEFAULT;
        }
        $lines = explode(PHP_EOL, wordwrap($message, $this->lineWidth, PHP_EOL, true));
        
        foreach ($lines as $lineNo => $line) {
            if (!$inline) {
                $line = str_pad(
                    $line,
                    $this->lineWidth-strlen($this->linePadding)*2
                );
                $line = $this->linePadding . $line . $this->linePadding;
            }
            
            
            $line = sprintf('<%s>%s</> ', $style, $line);
            $lines[$lineNo] = $line;
        }
        if ($addMargins) {
            array_unshift($lines, '');
            $lines[] = '';
        }
        $this->write($lines, !$inline);
    }


    /**
     * @param string $answer
     * @param string $cleanedAnswer
     * @param boolean $discloseNewValue
     * @return bool
     */
    protected function interactOnCleanedAnswer(&$answer, &$cleanedAnswer, $discloseNewValue = true)
    {
        if ($answer === $cleanedAnswer) {
            return true;
        }
        if (!$discloseNewValue) {
            $this->warning('Detected some invalid ASCII control characters in your input!');
            return false;
        } else {
            
            $this->warning(
                'Detected some ASCII control characters in your'
                . ' input. I\'ve cleaned them out for ya, though. This is the'
                . ' clean value:'
                . PHP_EOL . PHP_EOL
                . '   ' . $cleanedAnswer
                . PHP_EOL . PHP_EOL
            );
            $useCleanedAnswer = $this->confirm('Use this value?');
            
            if ($useCleanedAnswer) {
                $answer = $cleanedAnswer;
                return true;
            }
        }
        
        $cleanedAnswer = '';
        return false;
    }


    /**
     * {@inheritdoc}
     */
    public function ask($question, $default = null, $validator = null)
    {
        $answer = '(answer dummy)';
        $cleanedAnswer = '(cleaned answer dummy)';
        while ($answer !== $cleanedAnswer) {
            $answer = parent::ask($question, $default, $validator);
            $cleanedAnswer = StringUtility::removeAsciiControlCharacters($answer, $matches);
            $this->interactOnCleanedAnswer($answer, $cleanedAnswer);
        }
        return $answer;
    }

    /**
     * {@inheritdoc}
     */
    public function askHidden($question, $validator = null)
    {
        $answer = '(answer dummy)';
        $cleanedAnswer = '(cleaned answer dummy)';
        while ($answer !== $cleanedAnswer) {
            $answer = parent::askHidden($question, $validator);
            $cleanedAnswer = StringUtility::removeAsciiControlCharacters($answer);
            $this->interactOnCleanedAnswer($answer, $cleanedAnswer, false);
        }
        return $answer;
    }
}
