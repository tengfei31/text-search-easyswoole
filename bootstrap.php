<?php

use App\Command\Rabbitmq\Consumer;
use App\Command\Rabbitmq\Publisher;
use EasySwoole\EasySwoole\Command\CommandContainer;

CommandContainer::getInstance()->set(new Consumer);
CommandContainer::getInstance()->set(new Publisher);