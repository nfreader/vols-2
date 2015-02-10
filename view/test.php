<?php
var_dump(generatePasswordResetLink());
$slot = new slot();
var_dump($slot->canuserclaimSlot(6));
var_dump($slot->canuserclaimSlot(7));
var_dump($slot->canuserclaimSlot(8));
var_dump($slot->isUserOnShift(11,1));