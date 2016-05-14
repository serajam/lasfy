<?php

/**
 * Class Job_Ads_ResumesTable
 *
 * @author Fedor Petryk
 */
class Job_Ads_ResumesTable extends Job_Ads_TableGateway
{
    use Job_Ads_Pagination;

    const JOIN_RESUME = 'Resume';

    /** Table name */
    protected $_name = 'Resumes';

    protected $_primary = 'resumeId';

    protected $alias = 'r';

    protected $aliasTagsJoin = 'rt';

    protected $tagsJoinTable = 'ResumeTags';
}