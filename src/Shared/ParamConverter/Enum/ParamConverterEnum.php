<?php

namespace App\Shared\ParamConverter\Enum;

enum ParamConverterEnum: string
{
    case QUERY_OBJECT = 'app.query';
    case FOS_REST_REQUEST_BODY = 'fos_rest.request_body';
}
