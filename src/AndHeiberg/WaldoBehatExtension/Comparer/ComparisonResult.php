<?php

namespace AndHeiberg\WaldoBehatExtension\Comparer;

class ComparisonResult
{
    /**
     * Score for image differences.
     * 0  = Images were identical
     * 1+ = Larger differences reduces larger scores
     * 
     * @var integer
     */
    protected $score;

    /**
     * The diff of the two images.
     * 
     * @TODO: Should this be a url or an actual image?
     * 
     * @var string
     */
    protected $image;

    /**
     * ComparisonResult constructor.
     *
     * @param $score
     * @param $image
     */
    public function __construct($score, $image)
    {
        $this->score = $score;
        $this->image = $image;
    }

    /**
     * Were the two images identical.
     *
     * @return bool
     */
    public function match()
    {
        return $this->score !== false and $this->score == 0;
    }

    /**
     * Get difference score.
     *
     * @return int
     */
    public function getScore()
    {
        return $this->score;
    }

    /**
     * Get difference image.
     *
     * @return string
     */
    public function getImage()
    {
        return $this->image;
    }
}
