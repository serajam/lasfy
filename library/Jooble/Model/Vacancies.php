<?php

/**
 * Class Vacancies
 * Collection
 *
 * @author Fedor Petryk
 */
class Jooble_Model_Vacancies implements Iterator, Countable
{
    /**
     * @var array Array of Jooble_Model_Vacancy
     */
    protected $vacancies;

    /**
     * @var int
     */
    protected $pagesCount;

    /**
     * @var int
     */
    protected $count;

    /**
     * @return mixed
     */
    public function getVacancies()
    {
        return $this->vacancies;
    }

    /**
     * @param mixed $vacancies
     */
    public function setVacancies($vacancies)
    {
        $this->vacancies = $vacancies;
    }

    /**
     * @param Jooble_Model_Vacancy $vacancy
     */
    public function addVacancy(Jooble_Model_Vacancy $vacancy)
    {
        $this->vacancies[] = $vacancy;
    }

    /**
     * @return mixed
     */
    public function getPagesCount()
    {
        return $this->pagesCount;
    }

    /**
     * @param mixed $pagesCount
     */
    public function setPagesCount($pagesCount)
    {
        $this->pagesCount = $pagesCount;
    }

    public function count()
    {
        if (null === $this->count) {
            $this->count = count($this->vacancies);
        }

        return $this->count;
    }

    public function key()
    {
        return key($this->vacancies);
    }

    public function next()
    {
        return next($this->vacancies);
    }

    public function rewind()
    {
        if ($this->vacancies != null) {
            return reset($this->vacancies);
        }

        return false;
    }

    public function valid()
    {
        return (bool)$this->current();
    }

    public function current()
    {
        if ($this->vacancies == null) {
            return false;
        }

        $key = key($this->vacancies);

        if (array_key_exists($key, $this->vacancies)) {
            $result = $this->vacancies[$key];
        } else {
            $result = false;
        }

        return $result;
    }
}