<?php

class RssService extends Job_Ads_Service
{
    protected $_feedTemplate = [
        'title'       => '',
        'description' => '',
        'link'        => '',
        'charset'     => 'utf-8',
        'language'    => '',
        'entries'     => []

    ];

    public function getVacanciesRssFeed()
    {
        $vacancies = $this->_mapper->getVacancies(100);
        if (!empty($vacancies) && count($vacancies) > 0) {
            $lang                               = Zend_Registry::get('language');
            $locale                             = Zend_Registry::get('Zend_Locale');
            $this->_feedTemplate['title']       = __('meta_title');
            $this->_feedTemplate['link']        = 'http://lasfy.com';
            $this->_feedTemplate['description'] = __('meta_description');
            $this->_feedTemplate['language']    = $locale;
            foreach ($vacancies as $v) {
                $this->_feedTemplate['entries'][] = [
                    'title'       => $v['seat'],
                    'guid'        => 'v' . $v['vacancyId'],
                    'pubDate'     => $v['createdAt'],
                    'link'        => 'http://lasfy.com/' . $lang . '/default/search/get-ads/type/vacancy/vacancyId/' . $v['vacancyId'],
                    'description' => $v['vacancyDescription']
                ];
            }
            $feed = Zend_Feed::importArray($this->_feedTemplate, 'rss');

            return $feed;
        }

        return false;
    }

    public function getResumesRssFeed()
    {
        $resumes = $this->_mapper->getResumes(100);
        if (!empty($resumes) && count($resumes) > 0) {
            $lang                               = Zend_Registry::get('language');
            $locale                             = Zend_Registry::get('Zend_Locale');
            $this->_feedTemplate['title']       = __('meta_title');
            $this->_feedTemplate['link']        = 'http://lasfy.com';
            $this->_feedTemplate['description'] = __('meta_description');
            $this->_feedTemplate['language']    = $locale;
            foreach ($resumes as $r) {
                $this->_feedTemplate['entries'][] = [
                    'title'       => $r['seat'],
                    'guid'        => 'r' . $r['resumeId'],
                    'pubDate'     => $r['createdAt'],
                    'link'        => 'http://lasfy.com/' . $lang . '/default/search/get-ads/type/resume/resumeId/' . $r['resumeId'],
                    'description' => $r['goals']
                ];
            }
            $feed = Zend_Feed::importArray($this->_feedTemplate, 'rss');

            return $feed;
        }

        return false;
    }

    public function getBothRssFeed()
    {
        $resumes                            = $this->_mapper->getResumes(100);
        $vacancies                          = $this->_mapper->getVacancies(100);
        $lang                               = Zend_Registry::get('language');
        $locale                             = Zend_Registry::get('Zend_Locale');
        $this->_feedTemplate['title']       = __('meta_title');
        $this->_feedTemplate['link']        = 'http://lasfy.com/' . $lang . '/showAll';
        $this->_feedTemplate['description'] = __('meta_description');
        $this->_feedTemplate['language']    = $locale;
        if (!empty($vacancies) && count($vacancies) > 0) {
            foreach ($vacancies as $v) {
                $this->_feedTemplate['entries'][] = [
                    'title'       => __('vacancy') . ' - ' . $v['seat'],
                    'guid'        => 'v' . $v['vacancyId'],
                    'pubDate'     => $v['createdAt'],
                    'link'        => 'http://lasfy.com/' . $lang . '/default/search/get-ads/type/vacancy/vacancyId/' . $v['vacancyId'],
                    'description' => $v['vacancyDescription']
                ];
            }
        }
        if (!empty($resumes) && count($resumes) > 0) {
            foreach ($resumes as $r) {
                $this->_feedTemplate['entries'][] = [
                    'title'       => __('resume') . ' - ' . $r['seat'],
                    'guid'        => 'r' . $r['resumeId'],
                    'pubDate'     => $r['createdAt'],
                    'link'        => 'http://lasfy.com/' . $lang . '/default/search/get-ads/type/resume/resumeId/' . $r['resumeId'],
                    'description' => $r['goals']
                ];
            }
        }

        if (count($this->_feedTemplate['entries']) > 0) {
            $feed = Zend_Feed::importArray($this->_feedTemplate, 'rss');

            return $feed;
        }

        return false;
    }
}