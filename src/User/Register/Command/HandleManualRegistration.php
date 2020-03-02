<?php namespace Anomaly\UsersModule\User\Register\Command;

use Anomaly\Streams\Platform\Message\MessageManager;
use Anomaly\UsersModule\User\Contract\UserInterface;
use Anomaly\UsersModule\User\Notification\UserPendingActivation;
use Anomaly\UsersModule\User\Register\RegisterFormBuilder;
use Illuminate\Notifications\AnonymousNotifiable;

/**
 * Class HandleManualRegistration
 *
 * @link   http://pyrocms.com/
 * @author PyroCMS, Inc. <support@pyrocms.com>
 * @author Ryan Thompson <ryan@pyrocms.com>
 */
class HandleManualRegistration
{

    /**
     * The form builder.
     *
     * @var RegisterFormBuilder
     */
    protected $builder;

    /**
     * Create a new HandleManualRegistration instance.
     *
     * @param RegisterFormBuilder $builder
     */
    public function __construct(RegisterFormBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     * Handle the command.
     *
     * @param MessageManager $messages
     */
    public function handle(MessageManager $messages)
    {
        if (!is_null($message = $this->builder->getFormOption('pending_message'))) {
            $messages->info($message);
        }

        /* @var UserInterface $user */
        $user = $this->builder->getFormEntry();

        $recipients = config('anomaly.module.users::notifications.pending_user', []);

        foreach ($recipients as $email) {
            (new AnonymousNotifiable)
                ->route('mail', $email)
                ->notify(
                    new UserPendingActivation($user)
                );
        }
    }
}
