<?php

abstract class BlikEnum
{
    const BLIK_CODE_LENGTH = 6;

    const BLIK_STATUS_PENDING = 0;
    const BLIK_STATUS_SUCCESS = 1;
    const BLIK_STATUS_FAILURE = 2;
    const BLIK_STATUS_EXPIRED = 4;
}
