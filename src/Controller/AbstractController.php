<?php

declare(strict_types=1);

namespace App\Controller;

use App\Exception\NotFoundException;
use App\Exception\StorageException;
use App\Request;
use App\Model\NoteModel;
use App\View;
use App\Exception\ConfigurationException;

abstract class AbstractController   //klasa realizująca globalną obsługę żądań użytkownika i tworzenia nowych kontrolerów
{
    protected const DEFAULT_ACTION = 'list';

    private static array $configuration = [];

    protected NoteModel $noteModel;
    protected Request $request;
    protected View $view;

    public static function initConfiguration(array $configuration): void
    {
        self::$configuration = $configuration;
    }

    public function __construct(Request $request)
    {
        if (empty(self::$configuration['db'])) {
            throw new ConfigurationException('Configuration error');
        }

        $this->noteModel = new NoteModel(self::$configuration['db']);

        $this->request = $request;
        $this->view = new View();
    }

    final public function run(): void
    {
        try {
            $action = $this->action() . 'Action';

            if (!method_exists($this, $action)) {
                $action = self::DEFAULT_ACTION . 'Action';
                //jeżeli akcja nie istnieje, ktora chcemy wykonac, to uzytkownik zostanie przekierowany do akcji domyslnej
            }

            $this->$action();

            //z racji tego ze parametry wywolania nazywaja sie jak metody (parametr created wywoluje metode created),
            //to program wywola te metode, ktora nazywa sie tak jak parametr

        } catch (StorageException $e) {
            //Log::error($e->getPrevious());
            $this->view->render('error', ['message' => $e->getMessage()]);
        } catch (NotFoundException $e) {
            $this->redirect('./', ['error' => 'noteNotFound']);
        }
    }

    final protected function redirect(string $to, array $params): void
    {
        $location = $to;

        if (count($params)) {
            $queryParams = [];
            foreach ($params as $key => $value) {
                $queryParams[] = urlencode($key) . '=' . urlencode($value);
            }
            $queryParams = implode('&', $queryParams);
            $location .= '?' . $queryParams;
        }

        header("Location: $location");
        exit;
    }

    final private function action(): string
    {
        return $this->request->getParam('action', self::DEFAULT_ACTION);
    }
}