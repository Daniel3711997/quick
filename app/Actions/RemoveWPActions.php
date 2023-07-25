<?php

declare(strict_types=1);

namespace Quick\Actions;

use Quick\Annotations\Action;
use Quick\Interfaces\Action as ActionInterface;

/**
 * @Action(name="wp", method="construct", disabled=true)
 */
class RemoveWPActions implements ActionInterface {
    public static function construct(): void {
        global $removeAction;

        $removeAction['appPreload'](); // "appPreload" is the id of the action in the Preload class
    }
}
