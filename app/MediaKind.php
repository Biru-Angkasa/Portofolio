<?php

namespace App;

enum MediaKind: string
{
    case Image = 'image';
    case Pdf = 'pdf';
    case Link = 'link';
}
