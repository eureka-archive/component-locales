<?php

/**
 * Copyright (c) 2010-2016 Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Eureka\Component\Locales\Gettext\Console;

use Eureka\Eurekon;

/**
 * Console Abstraction class.
 * Must be parent class for every console script class.
 *
 * @author  Romain Cottard
 */
class POTemplateGenerator extends Eurekon\Console
{
    /**
     * @var boolean $executable Set to true to set class as an executable script
     */
    protected $executable = true;

    /**
     * @var boolean $executable Console script description.
     */
    protected $description = 'Orm generator';

    /**
     * Help method.
     *
     * @return void
     */
    public function help()
    {
        $style = new Eurekon\Style(' *** RUN - HELP ***');
        Eurekon\Out::std($style->color('fg', Eurekon\Style::COLOR_GREEN)->get());
        Eurekon\Out::std('');

        $help = new Eurekon\Help('...', true);
        $help->addArgument('o', 'output-file',  'Path & filename for the POT file to create.', true, true);
        $help->addArgument('i', 'input-path',   'Path to scan to search messages to translate.', true, true);
        $help->addArgument('p', 'project-name', 'Project name for the file.', true, false);

        $help->display();
    }

    /**
     * Run method.
     *
     * @return void
     */
    public function run()
    {
        $argument   = Eurekon\Argument::getInstance();
        $directory  = (string) $argument->get('output-file');
        $configName = (string) $argument->get('input-path');

        $messages = $this->findMessages();

        $this->writeFile($messages);
    }

    /**
     * Find message to translate.
     *
     * @return array
     */
    private function findMessages()
    {
        $path   = Eurekon\Argument::getInstance()->get('input-path');
        $output = [];

        //~ Find all file and lines with _('.+') pattern (Work only on unix system)
        exec('grep -Erin "_\(\'.{1,}?\'\)" ' . escapeshellarg($path), $output);

        $messages = [];

        //~ Foreach item found, get string to translate.
        foreach ($output as $line) {

            $fileName = substr($line, 0, $pos = (strpos($line, ':')));
            $num  = substr($line, $pos + 1, $pos = (strpos($line, ':', $pos + 1) - $pos - 1));
            $grep = substr($line, $pos + 1);

            if (0 < $nb = (int) preg_match_all('`\_\(\'(.+?)\'\)`', $grep, $matches)) {

                foreach ($matches[1] as $match) {
                    $string = $match;

                    if (!isset($messages[$string])) {

                        $messages[$string] = [];
                    }

                    if (!isset($messages[$string][$fileName])) {
                        $messages[$string][$fileName] = [];
                    }

                    $messages[$string][$fileName][] = $num;
                }
            }
        }

        return $messages;
    }

    /**
     * Write POT file.
     *
     * @param  string[] $messages
     * @return void
     */
    private function writeFile($messages)
    {
        $argument = Eurekon\Argument::getInstance();

        $file = new \SplFileObject(Eurekon\Argument::getInstance()->get('output-file'), 'w');

        //~ Write Headers POT File
        $file->fwrite('#, fuzzy' . "\n");
        $file->fwrite('msgid ""' . "\n");
        $file->fwrite('msgstr ""' . "\n");
        $file->fwrite('"Project-Id-Version: ' . $argument->get('p', 'project', 'Default') . '\n"' . "\n");
        $file->fwrite('"POT-Creation-Date: ' . date('Y-m-d H:i+0000') . '\n"' . "\n");
        $file->fwrite('"PO-Revision-Date: ' . date('Y-m-d H:i+0000') . '\n"' . "\n");
        $file->fwrite('"Last-Translator: \n"' . "\n");
        $file->fwrite('"Language-Team: \n"' . "\n");
        $file->fwrite('"MIME-Version: 1.0\n"' . "\n");
        $file->fwrite('"Content-Type: text/plain; charset=utf-8\n"' . "\n");
        $file->fwrite('"Content-Transfer-Encoding: 8bit\n"' . "\n\n");

        //$file->fwrite('msgid "align"' . "\n");
        //$file->fwrite('msgstr "_: the alignment (left or right) of your language goes here"' . "\n\n");

        foreach ($messages as $string => $files) {

            foreach ($files as $fileName => $lines) {
                $file->fwrite('#: ' . $fileName . ':' . implode(',', $lines) . "\n");
            }
            $file->fwrite('#. Context file: ' . $fileName . ':' . implode(',', $lines) . "\n");
            $file->fwrite('msgid  "' . $string . '"'. "\n");
            $file->fwrite('msgstr "' . $string . '"'. "\n\n");
        }
    }
}
