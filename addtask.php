<?php
require_once '../vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;

class Play extends Command
{
    protected static $defaultName = 'play';
    protected static $defaultDescription = 'Play the game!';
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        require_once 'getstacks.php';
        //on ouvre le fichier json
        $json = json_decode(file_get_contents('../deck.json'), true);
        $question = ['col' => $cols, 'titre' => '', 'description' => '', 'col' => $cols, 'jour' => "monday,thursday", 'heure' => 9, 'position' => "bottom"];
        $helper = $this->getHelper('question');
        foreach ($question as $q => $def) {
            if (strpos($def, ',') !== false) {

                $question = new ChoiceQuestion(
                    $q,
                    explode(',', $def),
                    0
                );
                $question->setErrorMessage('Color %s is invalid.');
                $reponse = $helper->ask($input, $output, $question);
            } else
                $reponse = readline("$q ($def)" . ': ');
            dd($reponse);
            $rep[$q] = $reponse == '' ? $def : $reponse;
        }
        $titre = $rep['titre'];
        unset($rep['titre']);
        $json['create'][$titre] = $rep;
        file_put_contents('../deck.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return Command::SUCCESS;
    }
}

use Symfony\Component\Console\Application;



$application = new Application();
$application->add(new Play());
$application->setDefaultCommand('play', true);
$application->run();
