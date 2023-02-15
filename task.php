<?php
require_once '../vendor/autoload.php';

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;




class Task extends Command
{
    protected static $defaultName = 'run';
    protected static $defaultDescription = '';

    private $input;
    private $output;
    private $json;
    private $jour = ['complete' => "monday,thursday"];
    private $heure = ['complete' => "9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 1, 2, 3, 4, 5, 6, 7, 8"];
    private $position = ['complete' => 'bottom', 'top'];
    private $col;

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->col = ['choice' => $this->getstacks()];
        $this->input = $input;
        $this->output = $output;
        $this->json = json_decode(file_get_contents('../deck.json'), true);
        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion(
            'Que voulez-vous faire?',
            ['Ajouter une tâche', 'Déplacer une tâche', 'Supprimer une tâche'],
            0
        );
        $reponse = $helper->ask($this->input, $this->output, $question);
        switch ($reponse) {
            case 'Ajouter une tâche':
                $this->addtask();
                break;
            case 'Déplacer une tâche':
                $this->movetask();
                break;
            case 'Supprimer une tâche':
                $this->deltask();
                break;
        }


        echo json_encode($this->json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        //file_put_contents('../deck.json', json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        return Command::SUCCESS;
    }
    private function poserQuestion($questions)
    {
        $rep = [];
        $helper = $this->getHelper('question');
        foreach ($questions as $q => $def) {
            switch (key($def)) {
                case 'choice':
                    $exp = array_map('trim', explode(',', current($def)));
                    $default = count($exp) - 1;
                    $question = new ChoiceQuestion(
                        ucfirst($q) . '[' . $exp[$default] . ']:',
                        $exp,
                        $default
                    );
                    $reponse = $helper->ask($this->input, $this->output, $question);
                    $pos = strpos($reponse, '(');
                    $rep[$q] = substr($reponse, 0, $pos);
                    break;
                case 'complete':
                    $question = new Question(
                        ucfirst($q) . ':'
                    );
                    $exp = array_map('trim', explode(',', current($def)));
                    $question->setAutocompleterValues($exp);
                    $reponse = $helper->ask($this->input, $this->output, $question);
                    $rep[$q] = $reponse;
                    break;
                default:
                    $question = new Question(
                        ucfirst($q) . ':'
                    );
                    $default = isset($def[0]) ? "($def[0])" : "";
                    $reponse = $helper->ask($this->input, $this->output, $question);
                    $rep[$q] = $reponse;
                    break;
            }
        }
        return $rep;
    }
    private function getstacks()
    {
        try {
            $stacks = exec('/home/debian/docker/applications/deck/get_stacks.sh' . ' 27');
        } catch (Exception $e) {
            exit('Exception reçue : ' . $e->getMessage());
        }
        $stacks = json_decode(substr($stacks, strlen('@json')), true);
        foreach ($stacks as $stack) {
            $col[] = $stack['id'] . "(" . $stack['title'] . ")";
        }
        return implode(', ', $col);
    }
    private function addtask()
    {
        $questions = ['titre' => [], 'description' => [], 'col' => $this->col, 'jour' => $this->jour, 'heure' => $this->heure, 'position' => $this->position];
        $rep = $this->poserQuestion($questions);
        $titre = $rep['titre'];
        unset($rep['titre']);
        $this->json['create'][$titre] = $rep;
    }
    private function deltask()
    {
        $questions = ['titre' => [], 'col' => $this->col, 'jour' => $this->jour, 'heure' => $this->heure];
        $rep = $this->poserQuestion($questions);
        $titre = $rep['titre'];
        unset($rep['titre']);
        $this->json['delete'][$titre] = $rep;
    }
    private function movetask()
    {
        $questions = ['titre' => [], 'colstart' => $this->col, 'colend' => $this->col, 'jour' => $this->jour, 'heure' => $this->heure];
        $rep = $this->poserQuestion($questions);
        $titre = $rep['titre'];
        unset($rep['titre']);
        $this->json['move'][$titre] = $rep;
    }
}

use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new Task());
$application->setDefaultCommand('run', true);
$application->run();
