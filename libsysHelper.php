<?php
/**
* Libsys图书馆管理系统 -- 助手
*/
class libsysHelper {
	
	private $libsys_home;					// libsys home address
	private $libsys_host;					// Host
	private $libsys_number;					// 用户名
	private $libsys_password;				// 密码
	private $libsys_select;					// 证件号[cert_no]/条码号[bar_no]/Email[email]
	private $libsys_cookies;				// 存储当前的cookies
	private $libsys_login;					// [我的图书馆] 登陆地址
	private $libsys_verify;					// login verify 
	private $libsys_redr_info;				// page of 证件信息
	private $libsys_book_lst;				// page of 当前借阅信息
	private $libsys_book_hist;				// page of 借阅历史信息
	private $libsys_ajax_renew;				// page of 续借
	private $libsys_ajax_topkeywords;		// page of 热门检索词
	private $libsys_ajax_top_lend_shelf;	// page of 热门借阅&热门图书
	private $libsys_openlink;				// page of search result
	private $libsys_ajax_item;				// the book item message
	private $libsys_top_lend;				// page of 热门借阅
	private $libsys_top_score;				// page of 热门评分
	private $libsys_top_shelf;				// page of 热门收藏
	private $libsys_top_book;				// page of 热门图书
	private $libsys_info_search;			// 欠款记录 超期图书记录

	/**
	 * 构造函数 
	 * @param string $libsys_number   user account
	 * @param string $libsys_password user password
	 * @param string $libsys_home     libsys host address
	 * @param string $libsys_select   select
	 */
	public function __construct($libsys_home, $libsys_host, $libsys_number, $libsys_password, $libsys_select = 'cert_no')
	{
		$this->libsys_home 						= $libsys_home;
		$this->libsys_host 						= $libsys_host;
		$this->libsys_number 					= $libsys_number;
		$this->libsys_password 					= $libsys_password;
		$this->libsys_select 					= $libsys_select;
		$this->libsys_login 					= 'http://' . $libsys_home . '/reader/login.php';
		$this->libsys_verify					= 'http://' . $libsys_home . '/reader/redr_verify.php';
		$this->libsys_redr_info					= 'http://' . $libsys_home . '/reader/redr_info.php';
		$this->libsys_book_lst					= 'http://' . $libsys_home . '/reader/book_lst.php';
		$this->libsys_book_hist					= 'http://' . $libsys_home . '/reader/book_hist.php';
		$this->libsys_ajax_renew				= 'http://' . $libsys_home . '/reader/ajax_renew.php';
		$this->libsys_ajax_topkeywords			= 'http://' . $libsys_home . '/opac/ajax_topkeywords.php';
		$this->libsys_ajax_top_lend_shelf		= 'http://' . $libsys_home . '/opac/ajax_top_lend_shelf.php';
		$this->libsys_openlink					= 'http://' . $libsys_home . '/opac/openlink.php';
		$this->libsys_ajax_item					= 'http://' . $libsys_home . '/opac/ajax_item.php';
		$this->libsys_top_lend					= 'http://' . $libsys_home . '/top/top_lend.php';
		$this->libsys_top_score					= 'http://' . $libsys_home . '/top/top_score.php';
		$this->libsys_top_shelf					= 'http://' . $libsys_home . '/top/top_shelf.php';
		$this->libsys_top_book					= 'http://' . $libsys_home . '/top/top_book.php';
		$this->libsys_info_search				= 'http://' . $libsys_home . '/info/info_search.php';

		if (is_null($libsys_number) or is_null($libsys_password)) {
			$this->libsys_cookies = '';
		}else{
			$this->getCookies();
		}
	}
	
	/**
	 * 获取的cookies
	 * @return string cookies
	 */
	private function getCookies()
	{
		$httpheader = array('Host: '.$this->libsys_host, 
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
			'Accept-Encoding: gzip, deflate',
			'DNT: 1',
			'Connection: keep-alive');
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->libsys_login);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_NOBODY, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		$content = curl_exec($ch);
		
		preg_match_all('#Set-Cookie:\s(.*);#', $content, $matches);
		$cookies = implode(';', $matches[1]);
		$this->libsys_cookies = $cookies;
		// return $cookies;
	}


	/**
	 * set httpheader
	 * @param string $host    host
	 * @param string $referer referer
	 */
	private function setHttpHeader($referer = 'http://www.baidu.com/')
	{
		$httpheader = array('Host: '.$this->libsys_host,
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
			'Accept-Encoding: gzip, deflate',
			'DNT: 1',
			'Referer: '.$referer, 
			'Cookie: '.$this->libsys_cookies,
			'Connection: keep-alive');
		return $httpheader;
	}

	private function setHttpHeaderNoCookie($referer = 'http://www.baidu.com/')
	{
		$httpheader = array('Host: '.$this->libsys_host,
			'User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:38.0) Gecko/20100101 Firefox/38.0',
			'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
			'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
			'Accept-Encoding: gzip, deflate',
			'DNT: 1',
			'Referer: '.$referer,
			'Connection: keep-alive');
		return $httpheader;
	}


	/**
	 * setCurl
	 * @param string $url        the value of CURLOPT_URL
	 * @param string $httpheader http request header
	 * @param string $post       post or get
	 * @param string $postData   post data
	 */
	private function setCurl($url, $httpheader, $post = 'false', $postData = '')
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $httpheader);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, $post);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
		$content = curl_exec($ch);
		return $content;
	}

	/**
	 * 用户名密码验证
	 * @param  string $libsys_verify url
	 * @return boolean. true for success or false for failed
	 */
	public function verify()
	{

		$returnUrl = '';
		$httpheader = $this->setHttpHeader();
		$postData = 'number=' . $this->libsys_number . '&passwd=' . $this->libsys_password . '&select=' . $this->libsys_select . '&returnUrl=';
		$content = $this->setCurl($this->libsys_verify, $httpheader, true, $postData);
		
		preg_match_all('#Location:\s(.*?)\s#', $content, $matches);
		if ($matches[1][0]) {	// login success
			return true;
		}else{	// login failed
			return false;
		}

	}

	/**
	 * 获取证件信息
	 * @param  string $libsys_redr_info url
	 * @return array for success or false for failed
	 */
	public function getUserMsg()
	{
		$httpheader = $this->setHttpHeader();
		$content = $this->setCurl($this->libsys_redr_info, $httpheader);

		preg_match_all('#bluetext">(.*?)：</span>(.*?)</TD#', $content, $matches);
		if ($matches) {		// get success 
			return array_splice($matches, 1, 2);
		}else{		// get failed
			return false;
		}
	}

	/**
	 * 获取当前借阅信息
	 * @param  string $libsys_book_lst url
	 * @return array for success or false for failed
	 */
	public function getCurrentMsg()
	{
		$httpheader = $this->setHttpHeader();
		$content = $this->setCurl($this->libsys_book_lst, $httpheader);

		preg_match_all('#<img src="\.\.(.*?)"#', $content, $qr);
		$qr = $qr[1][0];	// 当前借阅信息的二维码url
		preg_match_all('#<table(.*?)</table>#s', $content, $matches);
		$table = $matches[1][0];
		if ($table) {		// get success 
			preg_match_all('#<tr>(.*?)</tr>#s', $table, $matches);
			$content = $matches[1];
			for ($i=0; $i < count($content); $i++) { 
				preg_match_all('#>(.*?)</td>#', $content[$i], $temp);
				preg_match_all('#\,\'(.*?)\'#', $table, $matches17);	// renew param
				while ($i != 0) {
					preg_match_all('#no=(.*?)"#', $temp[1][1], $matches11);
					$temp[1][8] = $matches11[1][0];
					preg_match_all('#>(.*);#', $temp[1][1], $matches11);
					$temp[1][1] = str_replace('</a>', '', $matches11[1][0]);
					preg_match_all('#>(.*?)\s#', $temp[1][3], $matches13);
					$temp[1][3] = $matches13[1][0];
					$temp[1][7] = $matches17[1][2*($i-1)];
					break;
				}
				$res[$i] = $temp[1];
			}
			$res[0][8] = 'http://' . $this->libsys_home . $qr;
			return $res;
		}else{		// get failed
			return false;
		}
	}

	/**
	 * 续借
	 * @param  string $bar_code 图书条码号
	 * @param  string $check    unknow param 
	 * @return [type]           [description]
	 */
	public function renew($bar_code, $check)
	{
		$url = $this->libsys_ajax_renew . '?bar_code=' . $bar_code . '&check=' . $check . '&time=' . time();
		$httpheader = $this->setHttpHeader();
		$content = $this->setCurl($url, $httpheader);
		preg_match_all('#>(.*?)<#', $content, $promptMsg);
		preg_match_all('#Content-Length:\s(\d+)\s#', $content, $matches);
		$renewRs = $matches[1][0];
		if ($renewRs == 71) {	// exceed
			return json_encode(array("code" => "401", "msg" => $promptMsg[1][0]));
		}elseif ($renewRs == 18) {	// invalid call
			return json_encode(array("code" => "402", "msg" => $promptMsg[1][0]));
		}elseif ($renewRs == 65) {	// less than renew time
			return json_encode(array("code" => "403", "msg" => $promptMsg[1][0]));
		}else{	// unknow error
			return json_encode(array("code" => "404", "msg" => $promptMsg[1][0]));
		}
	}

	/**
	 * 获取借阅历史信息
	 * @param  string $libsys_book_hist url
	 * @return array for success or false for failed
	 */
	public function getHistory()
	{
		$httpheader = $this->setHttpHeader();
		$postData = 'para_string=all&topage=1';
		$content = $this->setCurl($this->libsys_book_hist, $httpheader, true, $postData);

		preg_match_all('#<table(.*?)</table#s', $content, $matches);
		$table = $matches[1][0];
		if ($table) {		// get success 
			preg_match_all('#<tr>(.*?)</tr>#s', $table, $matches);
			$content = $matches[1];
			for ($i=0; $i < count($content); $i++) { 
				preg_match_all('#>(.*?)</td>#', $content[$i], $temp);
				while ($i != 0) {
					preg_match_all('#no=(.*?)"#', $temp[1][2], $matches12);
					$temp[1][7] = $matches12[1][0];
					preg_match_all('#>(.*?)</a>#', $temp[1][2], $matches12);
					$temp[1][2] = $matches12[1][0];
					break;
				}
				$res[$i] = $temp[1];
			}
			return $res;
		}else{		// get failed
			return false;
		}
	}

	/**
	 * 获取热门检索词
	 * @return array [description]
	 */
	public function getTopKeywords()
	{
		$httpheader = $this->setHttpHeaderNoCookie();
		$content = $this->setCurl($this->libsys_ajax_topkeywords, $httpheader);
		$content = urldecode($content);
		preg_match_all('#\'>(.*?)<#', $content, $keywords);
		preg_match_all('#href=\'(.*?)\'>#', $content, $keywordsLink);
		$res[0] = $keywords[1];
		$res[1] = $keywordsLink[1];
		return $res;
	}

	/**
	 * 获取热门图书&热门借阅
	 * @return array [description]
	 */
	public function getTopLendAndShelf()
	{
		$httpheader = $this->setHttpHeader();
		$content = $this->setCurl($this->libsys_ajax_top_lend_shelf, $httpheader);
		preg_match_all('#right(.*?)</div#s', $content, $topBook);
		preg_match_all('#center(.*?)</div#s', $content, $topBorrow);

		preg_match_all('#span>(.*?)</span#', $topBook[1][0], $topBookName);
		preg_match_all('#href="(.*?)">#', $topBook[1][0], $topBookLink);
		preg_match_all('#span>(.*?)</span#', $topBorrow[1][0], $topBorrowName);
		preg_match_all('#href="(.*?)">#', $topBorrow[1][0], $topBorrowLink);

		for ($i=0; $i < count($topBookLink[1]); $i++) { 
			$topBookLink[1][$i] =  'http://' . $this->libsys_home . '/opac/' . $topBookLink[1][$i];
		}
		for ($i=0; $i < count($topBorrowLink[1]); $i++) { 
			$topBorrowLink[1][$i] =  'http://' . $this->libsys_home . '/opac/' . $topBorrowLink[1][$i];
		}

		$res[0][0] = $topBookName[1];
		$res[0][1] = $topBookLink[1];
		$res[1][0] = $topBorrowName[1];
		$res[1][1] = $topBorrowLink[1];
		return $res;
	}


	/**
	 * 获取热门推荐 		热门借阅 热门评分	热门收藏 热门图书
	 * @param  string $url [description]
	 * @return array      [description]
	 */
	private function getTop($url = '')
	{
		$httpheader = $this->setHttpHeaderNoCookie();
		$content = $this->setCurl($url, $httpheader);
		preg_match_all('#<tr(.*?)</tr#s', $content, $tr);
		for ($i=0; $i < count($tr[1]); $i++) { 
			preg_match_all('#whitetext">(.*?)</td#', $tr[1][$i], $td);
			preg_match_all('#">(.*?)</a#', $td[1][1], $bookName);
			preg_match_all('#marc_no=(.*?)">#', $td[1][1], $marc_no);
			$td[1][1] = $bookName[1][0];
			$td[1][count($td[1])] = $marc_no[1][0];
			if ($url == $this->libsys_top_score) {
				preg_match_all('#star(\d+)\.gif#', $td[1][5], $score);
				$td[1][5] = $score[1][0];
			}
			$res[$i] = $td[1];
		}
		return $res;
	}

	/**
	 * 获取热门推荐下的热门借阅信息
	 * @return array [description]
	 */
	public function getTopLend()
	{
		return $this->getTop($this->libsys_top_lend);
	}

	/**
	 * 获取热门推荐下的热门评分信息
	 * @return array [description]
	 */
	public function getTopScore()
	{
		return $this->getTop($this->libsys_top_score);

	}

	/**
	 * 获取热门推荐下的热门收藏信息
	 * @return array [description]
	 */
	public function getTopShelf()
	{
		return $this->getTop($this->libsys_top_shelf);
	}

	/**
	 * 获取热门推荐下的热门图书信息
	 * @return array [description]
	 */
	public function getTopBook()
	{
		return $this->getTop($this->libsys_top_book);
	}

	/**
	 * 搜索
	 * @param  string $strText       [description]
	 * @param  string $strSearchType [description]
	 * @param  string $match_flag    [description]
	 * @param  string $historyCount  [description]
	 * @param  string $doctype       [description]
	 * @param  string $displaypg     [description]
	 * @param  string $showmode      [description]
	 * @param  string $sort          [description]
	 * @param  string $orderby       [description]
	 * @param  string $location      [description]
	 * @return array                [description]
	 */
	public function search($strText = '', $strSearchType = 'title', $match_flag = 'forward', $historyCount = '1', $doctype = 'ALL', $displaypg = '100', $showmode = 'list', $sort = 'CATA_DATE', $orderby = 'desc', $location = 'All')
	{
		$httpheader = $this->setHttpHeader();
		$url = $this->libsys_openlink . '?strSearchType=' . $strSearchType . '&match_flag=' . $match_flag . '&forward=' . $forward . '&historyCount=' . $historyCount . '&strText=' . $strText . '&doctype=' . $doctype . '&displaypg=' . $displaypg . '&showmode=' . $showmode . '&sort=' . $sort . '&orderby' . $orderby . '&location=' . $location;
		$content = $this->setCurl($url, $httpheader);
		preg_match_all('#search_book_list">(.*?)/ol#s', $content, $matches);
		preg_match_all('#<li(.*?)</li#s', $matches[1][0], $matches);

		for ($i=0; $i < count($matches[1]); $i++) { 
			preg_match_all('#<h3><span>(.*?)</span>#', $matches[1][$i], $bookType);
			preg_match_all('#marc_no=(.*?)"#', $matches[1][$i], $marc_no);
			preg_match_all('#\d\.(.*?)</a>#', $matches[1][$i], $bookName);
			preg_match_all('#</a>\s+(.*?)\s</h3>#s', $matches[1][$i], $callNumber);
			preg_match_all('#馆藏复本：(\d+)\s<br>#', $matches[1][$i], $holding);
			preg_match_all('#可借复本：(\d+)</span#', $matches[1][$i], $surplus);
			preg_match_all('#/span>\s(.*?)<br />#s', $matches[1][$i], $author);
			preg_match_all('#<br />\s+(.*?)<br#s', $matches[1][$i], $publisher);

			$res[$i][0] = $bookType[1][0];
			$res[$i][1] = $marc_no[1][0];
			$res[$i][2] = $bookName[1][0];
			$res[$i][3] = $callNumber[1][0];
			$res[$i][4] = $holding[1][0];
			$res[$i][5] = $surplus[1][0];
			$res[$i][6] = $author[1][0];
			$res[$i][7] = $publisher[1][0];
		}
		return $res;
	}

	/**
	 * 获取某本图书馆藏信息
	 * @param  string $marc_no 
	 * @return array          [description]
	 */
	public function getAjaxItem($marc_no)
	{
		$httpheader = $this->setHttpHeaderNoCookie();
		$url = $this->libsys_ajax_item . '?marc_no=' . $marc_no;
		$content = $this->setCurl($url, $httpheader);
		preg_match_all('#whitetext"\s>(.*?)</tr#s', $content, $matches);
		for ($i=0; $i < count($matches[1]); $i++) { 
			preg_match_all('#[/|\s]>(.{3,}?)</#', $matches[1][$i], $item);
			preg_match_all('#>(.*)#', $item[1][4], $temp);
			$item[1][4] = $temp[1][0];
			$res[$i] = $item[1];
		}
		return $res;
	}

	/**
	 * 获取 欠款记录 超期图书记录
	 * @param  string $q      [description]
	 * @param  string $s_type [description]
	 * @return array         [description]
	 */
	public function getRecordOfArrears($q, $s_type = 'certid')
	{
		$httpheader = $this->setHttpHeaderNoCookie();
		$url = $this->libsys_info_search . '?s_type=' . $s_type . '&q=' . $q;
		$content = $this->setCurl($url, $httpheader);
		preg_match_all('#<b>([\d|\.]+)</b>#', $content, $matches);
		return $matches[1];
	}




}
