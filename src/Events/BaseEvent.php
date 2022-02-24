<?php

namespace ZeroDaHero\LaravelWorkflow\Events;

use Workflow;
use Symfony\Component\Workflow\WorkflowInterface;
use Symfony\Component\Workflow\Event\Event;
use Symfony\Component\Workflow\Marking;
use Symfony\Component\Workflow\Transition;

/**
 * @method Marking getMarking()
 * @method object getSubject()
 * @method Transition getTransition()
 * @method WorkflowInterface getWorkflow()
 * @method string getWorkflowName()
 * @method mixed getMetadata(string $key, $subject)
 */
abstract class BaseEvent extends Event
{
    public function __serialize(): array
    {
        return [
            'base_event_class' => get_class($this),
            'subject' => $this->getSubject(),
            'marking' => $this->getMarking(),
            'transition' => $this->getTransition(),
            'workflow' => [
                'name' => $this->getWorkflowName(),
            ],
        ];
    }

    public function __unserialize(array $data): void
    {
        $workflowName = $data['workflow']['name'] ?? null;
        parent::__construct(
            $data['subject'],
            $data['marking'],
            $data['transition'],
            Workflow::get($data['subject'], $workflowName)
        );
    }

    /**
     * Creates a new instance from the base Symfony event
     */
    public static function newFromBase(Event $symfonyEvent)
    {
        return new static(
            $symfonyEvent->getSubject(),
            $symfonyEvent->getMarking(),
            $symfonyEvent->getTransition(),
            $symfonyEvent->getWorkflow(),
            $symfonyEvent->getContext()
        );
    }
}
