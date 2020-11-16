<?php

use App\Command\Rabbitmq\Publisher;
use EasySwoole\EasySwoole\Command\CommandContainer;

CommandContainer::getInstance()->set(new Publisher);