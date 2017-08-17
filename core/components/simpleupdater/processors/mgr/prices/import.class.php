<?php

/**
 * Create a Link
 */
class rldImportProcessor extends modProcessor {
    public $languageTopics = array('simpleupdater');

    public function process() {
        $object = array();
        $object['log'] = array();
		$object['filename'] = $_POST['filename'] ? $_POST['filename'] : 'import_'.date('d-m-Y_His');
		$key = 'simpleupdater/import/'.$object['filename'];
		if (!$cache = $this->modx->cacheManager->get($key)) {
			$cache = array();
		}
        if ($_POST['parsed']) {
			$limit = 500;
			$offset = $_POST['step'] ? $_POST['step'] * $limit : 0;
			
            $data = array_slice($this->modx->cacheManager->get($key), 0, $limit);
			$remains = array_slice($this->modx->cacheManager->get($key), $limit);
			//$object['log'][] = $this->modx->toJSON(array_shift($data));
			$skus = array();
			foreach($data as $k => $row) {
				$skus[] = $row[0];
			}
			$skus = array_unique($skus);
			$skuQ = $this->modx->newQuery('modTemplateVarResource');
			$skuQ->select($this->modx->getSelectColumns('modTemplateVarResource','modTemplateVarResource',''), 'COUNT(*)');
			$skuQ->where(array('tmplvarid' => 2, 'value:IN' => $skus));
			$skuQ->groupby('value');
			$skuQ->having(array('COUNT(*) > 1'));
			$skuQ->prepare();
			$skuQ->stmt->execute();
			$skuTVdublicates = $skuQ->stmt->fetchAll(PDO::FETCH_ASSOC);
			$dublicates = array();
			foreach ($skuTVdublicates as $dublicate){
				$dublicates[] = $dublicate['value'];
				$i = array_search($dublicate['value'], $skus);
				if ($i !== false) {
					unset($skus[$i]);
				}
			}

			$q = $this->modx->newQuery('modTemplateVarResource');
			$q->select(
						$this->modx->getSelectColumns('modTemplateVarResource','modTemplateVarResource','').
						', `PRICE`.`id` AS `price_id`
						, `PRICE`.`value` AS `price_value`
						, `PRICE`.`tmplvarid` AS `price_tmplvarid`
						, `PRICE`.`contentid` AS `price_contentid`'
					);
			$q->where(array('`modTemplateVarResource`.`value`:IN' => $skus, 'AND:`modTemplateVarResource`.`tmplvarid`:=' => 2));
			$q->rightJoin('modTemplateVarResource', 'PRICE', '`modTemplateVarResource`.`contentid` = `PRICE`.`contentid` AND `PRICE`.`tmplvarid` = 3');
			$q->sortby('`modTemplateVarResource`.`value`','ASC');
			$q->prepare();
			$q->stmt->execute();
			$res = $q->stmt->fetchAll(PDO::FETCH_ASSOC);
			$prices = array();
			foreach($res as $priceTV) {
				$prices[$priceTV['value']] = $priceTV;
			}
			foreach($data as $k => $row) {
				$i = $offset + $k + 1;
				$sku = $row[0];
				$price = number_format($row[2], 2, '.', '');
				if (in_array($sku, $dublicates)) {
					$object['log'][] = '<span class="red">'.$i.'. Ошибка. Обнаружено несколько товаров с артикулом '.$sku.'</span>';
				} elseif(!isset($prices[$sku]) || empty($prices[$sku])) {
					if ($skuTV = $this->modx->getObject('modTemplateVarResource', array('value' => $sku, 'tmplvarid' => 2))){
						if ($resource = $this->modx->getObject('modResource', $skuTV->get('contentid'))) {
							if (number_format($resource->getTVValue('price'), 2, '.', '') == $price) {
								$object['log'][] = $i.'. Цена товара с артикулом '.$sku.' не изменилась';
							} else {
								$resource->setTVValue('price', $price);
								$resource->save();
								$object['log'][] = '<span class="green">'.$i.'. Товару с артикулом '.$sku.' установлена новая цена '.number_format($row[2], 2, ',', ' ').'</span>';
							}
						} else {
							$object['log'][] = '<span class="red">'.$i.'. Ошибка. Объект с артикулом '.$sku.' не найден.</span>';
						}
					} else {
						$object['log'][] = '<span class="red">'.$i.'. Ошибка. Неизвестная ошибка при обработке товара с артикулом '.$sku.'</span>';
					}
				} else {
					if (number_format($prices[$sku]['price_value'], 2, '.', '') == $price) {
						$object['log'][] = $i.'. Цена товара с артикулом '.$sku.' не изменилась';
					} else {
						if ($resourcePrice = $this->modx->getObject('modTemplateVarResource', $prices[$sku]['price_id'])) {
							$resourcePrice->set('value', $price);
							$resourcePrice->save();
							$object['log'][] = '<span class="green">'.$i.'. Товару с артикулом '.$sku.' установлена новая цена '.number_format($row[2], 2, ',', ' ').'</span>';
						} else {
							$object['log'][] = '<span class="red">'.$i.'. Ошибка. Неизвестная ошибка при обработке товара с артикулом '.$sku.'</span>';
						}
					}
				}
			}
            $this->modx->cacheManager->set($key, $remains);
            if (empty($remains)) {
                $object['complete'] = true;
                $object['log'][] = '<b>'.$this->modx->lexicon('finish').'</b>';
            }
            $object['step'] = $_POST['step'] + 1;
        } else {
            if (!empty($_FILES['csv-file']['name']) && !empty($_FILES['csv-file']['tmp_name'])) {
                $data = false;
				// Подключаем класс для работы с excel
				require_once($this->modx->getOption('core_path').'components/phpexcell/Classes/PHPExcel.php');
				//require_once($this->modx->getOption('core_path').'components/phpexcell/Classes/PHPExcel/Writer/Excel5.php');
				require_once($this->modx->getOption('core_path').'components/phpexcell/Classes/PHPExcel/IOFactory.php');
				// Открываем файл
				$xls = PHPExcel_IOFactory::load($_FILES['csv-file']['tmp_name']);
				// Устанавливаем индекс активного листа
				$xls->setActiveSheetIndex(0);
				// Получаем активный лист
				$sheet = $xls->getActiveSheet();
				// Получили строки и обойдем их в цикле
				$rowIterator = $sheet->getRowIterator();
				foreach ($rowIterator as $row) {
					// Получили ячейки текущей строки и обойдем их в цикле
					$cellIterator = $row->getCellIterator();
					$product = array();
					foreach ($cellIterator as $k => $cell) {
						if ($cell->getCalculatedValue()) {
							$product[$k] = $cell->getCalculatedValue();
						}
					}
					if (!empty($product)) {
						$data[] = $product;
					}
				}
                if (!$this->hasErrors()) {
                    if ($data === false) {
                        $this->modx->error->addField('csv-file-btn', $this->modx->lexicon('simpleupdater_import_fileuploadfailed'));
                    } else {
                        $this->modx->cacheManager->set($key, $data);
                        $object['log'][] = $this->modx->lexicon('simpleupdater_import_file_parsed') . ' ' . count($data);
                    }
                }
            } else {
                $this->modx->error->addField('csv-file-btn', $this->modx->lexicon('simpleupdater_import_fileuploadfailed'));
            }
        }

        if ($this->hasErrors()) {
            $o = $this->failure();
        } else {
            $o = $this->success('', $object);
        }
        return $o;
    }

}

return 'rldImportProcessor';