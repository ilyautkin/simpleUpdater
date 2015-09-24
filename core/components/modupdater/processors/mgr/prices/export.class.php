<?php

class rldExportProcessor extends modProcessor {
    public $languageTopics = array('modupdater');

    public function process() {
        $object = array();
        $object['log'] = array();
		$object['filename'] = $_POST['filename'] ? $_POST['filename'] : 'export_'.date('d-m-Y_His');
		$key = 'modupdater/export/'.$object['filename'];
		if (!$cache = $this->modx->cacheManager->get($key)) {
			$cache = array();
		}
		if (!$_POST['exported']) {
			$limit = 100;
			$offset = $_POST['step'] ? $_POST['step'] * $limit : 0;

			$q = $this->modx->newQuery('modResource');
			$q->select(
						'`modResource`.`id` AS `resource`,
						`modResource`.`pagetitle` as `pagetitle`,
						`SKU`.`value` AS `SKU`,
						`PRICE`.`value` AS `PRICE`'
					);
			$q->where(array('`SKU`.`value`:!=' => '', 'AND:`SKU`.`tmplvarid`:=' => 2));
			$q->rightJoin('modTemplateVarResource', 'SKU', '`modResource`.`id` = `SKU`.`contentid` AND `SKU`.`tmplvarid` = 2');
			$q->leftJoin('modTemplateVarResource', 'PRICE', '`modResource`.`id` = `PRICE`.`contentid` AND `PRICE`.`tmplvarid` = 3');
			$q->sortby('`SKU`.`value`','ASC');
			$count = $this->modx->getCount('modResource', $q);
			if ($offset == 0) {
				$object['log'][] = 'Общее количество товаров, у которых указан артикул: '.$count;
			}
			$q->limit($limit, $offset);
			$q->prepare();
			//print $q->toSQL();
			//print $this->modx->getCount('modTemplateVarResource', $q);
			$q->stmt->execute();
			$res = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
			$this->modx->cacheManager->set($key, array_merge($cache, $res));
			
			$total = $offset + $limit;
			if ($count <= $total) {
				$object['exported'] = true;
				$total = $count;
			}
			$object['step'] = $_POST['step'] + 1;
			$object['log'][] = $object['step'].'. Выгружено товаров '.$total.' из '.$count;
			if ($object['exported'] == true) {
				$object['log'][] = '<span id="creating-xls" class="loading-indicator">Товары выгружены. Создаём XLS-файл...</span>';
			}
		} else {
			// Подключаем класс для работы с excel
			require_once($this->modx->getOption('core_path').'components/phpexcell/Classes/PHPExcel.php');
			require_once($this->modx->getOption('core_path').'components/phpexcell/Classes/PHPExcel/Writer/Excel5.php');
			
			$xls = new PHPExcel();
			// Устанавливаем индекс активного листа
			$xls->setActiveSheetIndex(0);
			// Получаем активный лист
			$sheet = $xls->getActiveSheet();
			// Подписываем лист
			$sheet->setTitle('База товаров сайта');

			for ($i = 0; $i < count($cache); $i++) {
				$cache[$i]['PRICE'] = str_replace(" ","", $cache[$i]['PRICE']);
				$cache[$i]['PRICE'] = str_replace(",",".", $cache[$i]['PRICE']);
				$sheet->setCellValueByColumnAndRow(0,$i+1,$cache[$i]['SKU']);
				$sheet->setCellValueByColumnAndRow(1,$i+1,$cache[$i]['pagetitle']);
				$sheet->setCellValueByColumnAndRow(2,$i+1,$cache[$i]['PRICE']);
				
				// Применяем выравнивание
				$sheet->getStyleByColumnAndRow(0, $i)->getAlignment()->
						setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);
				$sheet->getStyleByColumnAndRow(2, $i)->getAlignment()->
						setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_RIGHT);
				$sheet->getStyleByColumnAndRow(2, $i)->getNumberFormat()->
						setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
			}
			$sheet->getColumnDimension('A')->setAutoSize(true);
			$sheet->getColumnDimension('B')->setAutoSize(true);
			$sheet->getColumnDimension('C')->setAutoSize(true);
			// Выводим содержимое файла
			$objWriter = new PHPExcel_Writer_Excel5($xls);
			$cacheDir = $this->modx->getOption('core_path').'cache/default/modupdater/export/';
			$object['filepath'] = $cacheDir.$object['filename'].'.xls';
			$objWriter->save($object['filepath']);
			$object['filepath'] = str_replace($this->modx->getOption('core_path'), 'core/', $object['filepath']);
			$object['complete'] = true;
		}
		if ($object['complete'] == true) {
			$object['log'][] = '<b>'.$this->modx->lexicon('finish').'</b>';
			$this->modx->cacheManager->delete($key);
		}

        if ($this->hasErrors()) {
            $o = $this->failure();
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'rldExportProcessor';