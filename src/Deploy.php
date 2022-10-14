<?php

namespace LTL\Deploy;

use Composer\Script\Event;

class Deploy
{
    public static function execute(Event $event)
    {
        $arguments = self::resolveArguments($event->getArguments());

        $message = 'Generic message';
        
        if (isset($arguments['m'])) {
            $message = $arguments['m'];
        }

        $untracking = shell_exec('git status');
        $search = 'nothing to commit, working tree clean';
  

        if (\str_contains($untracking, $search)) {
            print(PHP_EOL);
            print("\033[0;33m". str_repeat('-', 35) ."\033[0m".PHP_EOL);
            print("\033[0;33m Nothing to commit\033[0m".PHP_EOL);
            print("\033[0;33m". str_repeat('-', 35) ."\033[0m".PHP_EOL);

            die();
        }


        print("\033[0;32m". str_repeat('-', 35) ."\033[0m".PHP_EOL);
        print("\033[0;32m\033[1m Sync Repository\033[0m".PHP_EOL);
        print("\033[0;32m". str_repeat('-', 35) ."\033[0m".PHP_EOL);
        shell_exec('git status');
        shell_exec('git checkout -b temp-branch');
        shell_exec('git checkout main');
        shell_exec('git pull');
        shell_exec('git merge temp-branch');
        shell_exec('git branch -D temp-branch');
        print(PHP_EOL);
     
        print("\033[0;32m". str_repeat('-', 35) ."\033[0m".PHP_EOL);
        print("\033[0;32m\033[1m Commit and Push main branch\033[0m".PHP_EOL);
        print("\033[0;32m". str_repeat('-', 35) ."\033[0m".PHP_EOL);
        shell_exec('git add .');
        shell_exec('git commit -m "'. $message .'"');
        shell_exec('git push origin main');
        print(PHP_EOL);
    }

    private static function resolveArguments(array $arguments): array
    {
        $result = [];
        foreach ($arguments as $argument) {
            $argument = explode('=', $argument);

            if (count($argument) !== 2) {
                continue;
            }

            $result[$argument[0]] = $argument[1];
        }

        return $result;
    }
}