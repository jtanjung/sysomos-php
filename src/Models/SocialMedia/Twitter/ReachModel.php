<?php namespace SysOmos\SocialMedia\Twitter\Models;

use SysOmos\Bases\Model;
use SysOmos\Models\AuthorityBreakdownModel;
use SysOmos\Models\MentionTypeModel;
use SysOmos\Models\EngagementLevelModel;

/**
 * Class ReachModel
 * @package SysOmos\Models
 */
class ReachModel extends Model {
		
    /**
     * Estimated impressions
     *
     * @param string
     */
	public $Impressions;
		
    /**
     * Mentions count
     *
     * @param int
     */
	public $Mentions;
		
    /**
     * Users count
     *
     * @param int
     */
	public $Users;
		
    /**
     * Authority breakdown statistic data
     *
     * @param AuthorityBreakdownModel
     */
	public $AuthorityBreakdown;
		
    /**
     * Mention type statistic data
     *
     * @param MentionTypeModel
     */
	public $MentionType;
		
    /**
     * Engagement level statistic data
     *
     * @param EngagementLevelModel
     */
	public $EngagementLevel;
	
    /**
     * Class constructor
     *
     */
    public function __construct()
    {
		parent::__construct();
		$this->AuthorityBreakdown = new AuthorityBreakdownModel();
		$this->MentionType = new MentionTypeModel();
		$this->EngagementLevel = new EngagementLevelModel();
    }
}
