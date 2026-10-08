<?php

namespace App;

enum WorkStatus: string
{
    case Draft = 'draft';
    case Published = 'published';
}
