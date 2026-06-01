<?php

namespace App\Shared\Enums;

enum ClaimStatus: string
{
    case Pending = 'pending';
    case Submitted = 'submitted';
    case InReview = 'in_review';
    case Approved = 'approved';
    case PartiallyApproved = 'partially_approved';
    case Rejected = 'rejected';
    case Appealed = 'appealed';
}
