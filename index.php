<?php
/**
 * Random StarTrek episode selector
 *
 * PHP version 7.2
 *
 * @category Trekisode
 * @package  Trekisode
 * @author   Frans-Willem Post (FWieP) <fwiep@fwiep.nl>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://fwiep.nl/
 */

/**
 * StarTrek series
 *
 * @category Series
 * @package  Trekisode
 * @author   Frans-Willem Post (FWieP) <fwiep@fwiep.nl>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://fwiep.nl/
 */
class Series
{
    /**
     * The series' mnemonic, three characters code 
     * 
     * @var string
     */
    public $code = '';
    
    /**
     * The series' full name
     * 
     * @var string
     */
    public $name = '';
    
    /**
     * The series' seasons
     * 
     * @var Season[]
     */
    public $seasons = array();
    
    /**
     * Creates a new series
     * 
     * @param string $code the code
     * @param string $name the name
     */
    public function __construct(string $code, string $name) 
    {
        $this->code = $code;
        $this->name = $name;
    }
}

/**
 * StarTrek series' season
 *
 * @category Season
 * @package  Trekisode
 * @author   Frans-Willem Post (FWieP) <fwiep@fwiep.nl>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://fwiep.nl/
 */
class Season
{
    /**
     * The season's sequential number
     * 
     * @var int
     */
    public $number = 0;
    
    /**
     * The season's episodes
     * 
     * @var Episode[]
     */
    public $episodes = array();
    
    /**
     * Creates a new Season
     * 
     * @param int $number the number
     */
    public function __construct(int $number) 
    {
        $this->number = $number;
    }
}

/**
 * StarTrek episode
 *
 * @category Episode
 * @package  Trekisode
 * @author   Frans-Willem Post (FWieP) <fwiep@fwiep.nl>
 * @license  https://www.gnu.org/copyleft/gpl.html GNU General Public License
 * @link     https://fwiep.nl/
 */
class Episode
{
    /**
     * The episodes' sequential number
     * 
     * @var int
     */
    public $number = 0;
    
    /**
     * The full name
     * 
     * @var string
     */
    public $name = '';
    
    /**
     * The episode's airdate in UNIX timestamp format
     * 
     * @var int
     */
    public $airdate = 0;

    /**
     * Creates a new episode
     * 
     * @param int    $number  the number 
     * @param string $name    the name
     * @param int    $airdate the airdate in UNIX timestamp format
     */
    public function __construct(int $number, string $name, int $airdate) 
    {
        $this->number = $number;
        $this->name = $name;
        $this->airdate = $airdate;
    }
}

$x = new DOMDocument();
$o = array();
$p = dirname(__FILE__);

if (@$x->load($p.'/data.xml') && @$x->schemaValidate($p.'/schema.xsd')) {
    foreach ($x->getElementsByTagName('Series') as $series) {
        $sr = new Series(
            $series->getAttribute('code'),
            $series->getAttribute('name')
        );
        foreach ($series->getElementsByTagName('Season') as $season) {
            $se = new Season((int)$season->getAttribute('number'));
            
            foreach ($season->getElementsByTagName('Episode') as $episode) {
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
    
    $randSeries = $o[array_rand($o)];
    $randSeason = $randSeries->seasons[array_rand($randSeries->seasons)];
    $randEpisode = $randSeason->episodes[array_rand($randSeason->episodes)];
    
    $outputFormat = "<pre>Today's random StarTrek episode:<br />".
        "<a href=\"http://en.memory-alpha.org/wiki/%1\$s\">%1\$s</a> ".
        "<a href=\"http://en.memory-alpha.org/wiki/%1\$s_Season_%2\$d\"".
        ">%2\$dx%3\$02d</a> ".
        "<a href=\"http://en.memory-alpha.org/wiki/%5\$s_(episode)\">%4\$s</a>".
        "</pre>";
    
    if (isset($argv) && $argc > 0) {
        $outputFormat = "Today's random StarTrek episode:\n".
            "%1\$s %2\$dx%3\$02d %4\$s\n";
    }
    
    printf(
        $outputFormat,
        $randSeries->code,
        $randSeason->number,
        $randEpisode->number,
        $randEpisode->name,
        str_replace(' ', '_', $randEpisode->name)
    );
}