<?php

error_reporting(E_ALL);
ini_set('display_errors', '1');
date_default_timezone_set(@date_default_timezone_get());

$x = new DOMDocument();
$o = array();
$p = dirname(__FILE__);

/* @var $series DOMElement  */
/* @var $season DOMElement  */
/* @var $episode DOMElement  */

if(@$x->load($p.'/data.xml') && @$x->schemaValidate($p.'/schema.xsd'))
{
	foreach ($x->getElementsByTagName('Series') as $series)
	{
		$sr = new Series(
			$series->getAttribute('code'),
			$series->getAttribute('name')
		);
		
		foreach ($series->getElementsByTagName('Season') as $season)
		{
			$se = new Season(
				(int)$season->getAttribute('number')
			);
			
			foreach ($season->getElementsByTagName('Episode') as $episode)
			{
				$ep = new Episode(
					(int)$episode->getAttribute('number'),
					(string)$episode->getAttribute('name'),
					(int)strtotime($episode->getAttribute('airdate'))
				);
				
				$se->episodes[] = $ep;
			}
			$sr->seasons[] = $se;
		}
		$o[] = $sr;
	}
	
	/* @var $randSeries DOMElement  */
	/* @var $randSeason DOMElement  */
	/* @var $randEpisode DOMElement  */
	$randSeries = $o[array_rand($o)];
	$randSeason = $randSeries->seasons[array_rand($randSeries->seasons)];
	$randEpisode = $randSeason->episodes[array_rand($randSeason->episodes)];
	
	$outputFormat =
		"<pre>Today's random StarTrek episode:<br />".
		"<a href=\"http://en.memory-alpha.org/wiki/%1\$s\">%1\$s</a> ".
		"<a href=\"http://en.memory-alpha.org/wiki/%1\$s_Season_%2\$d\">%2\$dx%3\$02d</a> ".
		"<a href=\"http://en.memory-alpha.org/wiki/%5\$s_(episode)\">%4\$s</a>".
		"</pre>"; 
	
	if(isset($argv) && $argc > 0)
	{
		$outputFormat =
			"Today's random StarTrek episode:\n".
			"%1\$s %2\$dx%3\$02d %4\$s\n";
	}
	
	printf($outputFormat,
		$randSeries->code,
		$randSeason->number,
		$randEpisode->number,
		$randEpisode->name,
		str_replace(' ', '_', $randEpisode->name)
	);
}
else
{
	echo "Fout!\n";
}

class Series
{
	/**
	 * @var string
	 */
	public $code;
	
	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var array
	 */
	public $seasons;
	
	public function __construct($code, $name)
	{
		$this->code = $code;
		$this->name = $name;
	}
}

class Season
{
	/**
	 * @var int
	 */
	public $number;
	
	/**
	 * @var array
	 */
	public $episodes;
	
	public function __construct($number)
	{
		$this->number = $number;
	}
}

class Episode
{
	/**
	 * @var int
	 */
	public $number;

	/**
	 * @var string
	 */
	public $name;
	
	/**
	 * @var int
	 */
	public $airdate;
	
	public function __construct($number, $name, $airdate)
	{
		$this->number = $number;
		$this->name = $name;
		$this->airdate = $airdate;
	}
}

?>
