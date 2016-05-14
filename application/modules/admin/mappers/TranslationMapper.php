<?php

/**
 *
 * RolesMapper class
 *
 * @author     Fedor Petryk
 * @copyright  Copyright (c) 2013-2014 Studio 105 (http://105.in.ua)
 */
class TranslationMapper extends Core_Mapper_Super
{
    /**
     *
     * DbTable Class
     *
     * @var TranslationTable
     */
    protected $_tableName = 'TranslationTable';

    /**
     *
     * Rols row class
     *
     * @var Role
     */
    protected $_rowClass = 'Translation';

    public function add($data, $lang)
    {
        foreach ($lang as $langa => $val) {

            $dataIns = [
                'lang'    => $langa,
                'code'    => $data['code'],
                'caption' => $data['caption_' . $langa]
            ];
            $this->getDbTable()->insert($dataIns);
        }

        return true;
    }

    public function saveTranslation($data)
    {
        $tName = $this->getDbTable()->getName();
        $db    = $this->getDbTable()->getAdapter();
        $db->beginTransaction();

        foreach ($data as $code => $trans) {
            foreach ($trans as $lang => $caption) {
                if (is_array($caption)) {
                    if (array_key_exists('new', $caption)) {
                        $transRow                = $this->getTranslationRow($code);
                        $transRow->caption       = $caption['new'];
                        $transRow->lang          = $lang;
                        $transRow->translationId = null;

                        $db->insert(
                            $tName,
                            $transRow->toArray()
                        );
                    }
                } else {
                    $db->update(
                        $tName,
                        ['caption' => $caption],
                        [
                            'code = ?' => $code,
                            'lang = ?' => $lang
                        ]
                    );
                }
            }
        }

        try {
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();

            return $e->getMessage();
        }

        return true;
    }

    public function getTranslationRow($code)
    {
        $translation = $this->getDbTable()->fetchRow('code = "' . $code . '"');
        if (empty($translation)) {
            return false;
        }
        $translationArray = $translation->toArray();
        if (!empty($translationArray)) {
            return new Translation($translationArray);
        }

        return false;
    }

    public function getTranslation($search = null)
    {
        $table = $this->getDbTable()->getName();
        $db    = $this->getAdapter();
        if (!empty($search)) {
            $search = trim($search);
            $subSQL = $this->getAdapter()->select()
                ->from(['tr' => $table], ['code'])
                ->where(
                    'tr.caption LIKE ' . $db->quote('%' . $search . '%') . '
                    OR tr.code LIKE ' . $db->quote('%' . $search . '%')
                );
            $sql    = $this->getAdapter()->select()
                ->from(['tran' => $table])
                ->where('tran.code IN (' . (new Zend_Db_Expr($subSQL)) . ')')
                ->order('tran.code');

            $translation = $this->getAdapter()->fetchAll($sql);
        } else {
            $translation = $this->getDbTable()->fetchAll();
        }

        $collection = new TranslationsCollection();
        $collection->populate($translation);

        return $collection;
    }
}