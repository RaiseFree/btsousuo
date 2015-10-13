<?php
/**
 * Search
 *
 * @desc $Id$
 *
 * @author Michael Song 2015-09-28
 */
class Search
{
    private $_sphinx    = null;
    private $_q         = "";
    private $_sql       = "";
    private $_mode      = SPH_MATCH_ALL;
    private $_host      = "";
    private $_port      = null;
    private $_index     = "*";
    private $_groupby   = "";
    private $_groupsort = "@group desc";
    private $_filter    = "group_id";
    private $_filtervals = array();
    private $_distinct  = "";
    private $_sortby    = "";
    private $_limit     = 20;
    private $_ranker    = SPH_RANK_PROXIMITY_BM25;
    private $_select    = "";
    private $_sortexpr  = "";
    /**
     * 
     */
    function __construct()
    {
        $this->_sphinx = new SphinxClient();
        $this->_host   = \Yaf\Application::app()->getConfig()->search['sphinx']['host'];
        $this->_port   = intval(\Yaf\Application::app()->getConfig()->search['sphinx']['port']);
        $this->init();
    }

    public function setQ($val) 
    {
        $this->_q = $val;
    }

    public function setSortby($sortby)
    {
        if ($sortby) {
            $this->_sortby = $sortby;
            $this->_sphinx->SetSortMode(SPH_SORT_ATTR_DESC, $this->_sortby);
        }
        return $this;
    }

    public function setSortexpr($sortexpr = 'DESC')
    {
        $this->_sortexpr = $sortexpr;
        return $this;
    }

    public function setPage($page = 1) 
    {
        if ($this->_limit ) {
            $offset = ($page - 1) * $this->_limit;
            $this->_sphinx->SetLimits($offset, $this->_limit, ($this->_limit>1000) ? $this->_limit : 1000 );
        }	
        return $this;
    }
    /**
     * @desc   初始化sphinx搜索对象，并对每部分参数设置初始值
     * @param  
     * @return  
     */
    public function init() 
    {
        $this->_sphinx->SetServer($this->_host, $this->_port);
        $this->_sphinx->SetConnectTimeout(1);
        $this->_sphinx->SetArrayResult(true);
        $this->_sphinx->SetWeights(array(100, 1));
        $this->_sphinx->SetMatchMode($this->_mode);
        if (count($this->_filtervals))	$this->_sphinx->SetFilter($this->_filter, $this->_filtervals);
        if ($this->_groupby )		    $this->_sphinx->SetGroupBy($this->_groupby, SPH_GROUPBY_ATTR, $this->_groupsort);
        if ($this->_sortby )	        $this->_sphinx->SetSortMode(SPH_SORT_EXTENDED, $this->_sortby);
        if ($this->_sortexpr )			$this->_sphinx->SetSortMode(SPH_SORT_EXPR, $this->_sortexpr);
        if ($this->_distinct )			$this->_sphinx->SetGroupDistinct($this->_distinct);
        if ($this->_select )	        $this->_sphinx->SetSelect($this->_select);
        if ($this->_limit )				$this->_sphinx->SetLimits(0, $this->_limit, ($this->_limit>1000) ? $this->_limit : 1000 );
        $this->_sphinx->SetRankingMode ( $this->_ranker );
    }

    /**
     * @desc  
     * @param  
     * @return  
     */
    public function query() 
    {
        $res = $this->_sphinx->Query($this->_q, $this->_index);
        $print = '';
        if ( $res===false )
        {
            $print .= "Query failed: " . $this->_sphinx->GetLastError() . ".\n";

        } else {
            if ( $this->_sphinx->GetLastWarning() )
                $print .= "WARNING: " . $this->_sphinx->GetLastWarning() . "\n\n";

            $print .= "Query '$this->_q' retrieved $res[total] of $res[total_found] matches in $res[time] sec.\n";
            $print .= "Query stats:\n";
            if ( is_array($res["words"]) )
                foreach ( $res["words"] as $word => $info )
                    $print .= "    '$word' found $info[hits] times in $info[docs] documents\n";
            $print .= "\n";

            if (isset($res["matches"]) && is_array($res["matches"]) )
            {
                $n = 1;
                $print .= "Matches:\n";
                foreach ( $res["matches"] as $docinfo )
                {
                    $print .= "$n. doc_id=$docinfo[id], weight=$docinfo[weight]";
                    foreach ( $res["attrs"] as $attrname => $attrtype )
                    {
                        $value = $docinfo["attrs"][$attrname];
                        if ( $attrtype & SPH_ATTR_MULTI )
                        {
                            $value = "(" . join ( ",", $value ) .")";
                        } else
                        {
                            if ( $attrtype==SPH_ATTR_TIMESTAMP )
                                $value = date ( "Y-m-d H:i:s", $value );
                        }
                        $print .= ", $attrname=$value";
                    }
                    $print .= "\n";
                    $n++;
                }
            }
            if ($res['total']) {
                $res['pages'] = ceil($res['total']/$this->_limit);
            }
        }
        return $res;
    }
}
