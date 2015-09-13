<?php
class ModuleShop {
	private $IM;
	private $Module;
	
	private $lang = null;
	private $table;
	private $boardPages = array();
	private $postPages = array();
	
	private $boards = array();
	private $categorys = array();
	private $posts = array();
	private $ments = array();
	
	function __construct($IM,$Module) {
		$this->IM = $IM;
		$this->Module = $Module;
		
		$this->table = new stdClass();
		$this->table->category = 'shop_category_table';
		$this->table->item = 'shop_item_table';
		$this->table->item_option = 'shop_item_option_table';
		$this->table->promotion = 'shop_promotion_table';
		$this->table->attachment = 'shop_attachment_table';

		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/shop.js');
		$this->IM->addSiteHeader('style',$this->Module->getDir().'/styles/style.css');
		
		$this->Module->getInstalled()->promotionTimeInterval = 10;
	}
	
	function db() {
		return $this->IM->db($this->Module->getInstalled()->database);
	}
	
	function getLanguage($code) {
		if ($this->lang == null) {
			if (file_exists($this->Module->getPath().'/languages/'.$this->IM->language.'.json') == true) {
				$this->lang = json_decode(file_get_contents($this->Module->getPath().'/languages/'.$this->IM->language.'.json'));
			} else {
				$this->lang = json_decode(file_get_contents($this->Module->getPath().'/languages/'.$this->Module->getInfo()->languages[0].'.json'));
			}
		}
		
		$temp = explode('/',$code);
		if (count($temp) == 1) {
			return isset($this->lang->$code) == true ? $this->lang->$code : '';
		} else {
			$string = $this->lang;
			for ($i=0, $loop=count($temp);$i<$loop;$i++) {
				if (isset($string->$temp[$i]) == true) $string = $string->$temp[$i];
				else return '';
			}
			return $string;
		}
	}
	
	function getCountInfo($bid,$config) {
		return null;
		/*
		$board = $this->getBoard($bid);
		if ($board == null) return null;
		
		$info = new stdClass();
		
		if ($config == null) {
			$info->count = $board->postnum;
			$info->last_time = $board->last_post;
		} elseif (isset($config->category) == true) {
			if ($board->use_category == 'USEDALL') {
				$count = $board->postnum;
				$categorys = $this->db()->select($this->table->category)->where('bid',$bid)->get();
				for ($i=0, $loop=count($categorys);$i<$loop;$i++) {
					if ($categorys[$i]->idx != $config->category) $count = $count - $categorys[$i]->postnum;
				}
				$info->count = $count;
			} else {
				$info->count = $this->getCategory($config->category)->postnum;
			}
			$info->last_time = $this->getCategory($config->category)->last_post;
		}
		
		return $info;
		*/
	}
	
	function getContext($container) {
		$values = new stdClass();
		
		$templetPath = $this->Module->getPath().'/templets/'.$container;
		$templetDir = $this->Module->getDir().'/templets/'.$container;
		
		if (file_exists($templetPath.'/styles/style.css') == true) {
			$this->IM->addSiteHeader('style',$templetDir.'/styles/style.css');
		}
		
		if (file_exists($templetPath.'/scripts/script.js') == true) {
			$this->IM->addSiteHeader('script',$templetDir.'/scripts/script.js');
		}
		
		$context = '';
		
		switch ($container) {
			case 'seller' :
				$context.= $this->getSellerContext();
				break;
				
			case 'view' :
				$context.= $this->getViewContext($bid,$config);
				break;
				
			case 'write' :
				$context.= $this->getWriteContext($bid,$config);
				break;
		}
		
		$this->IM->fireEvent('afterGetContext','shop',$container,null,null,$context);
		
		return $context;
	}
	
	function getTable($table) {
		return $this->table->$table;
	}
	
	function getSellerContext() {
		ob_start();
		
//		$this->IM->removeTemplet();
		
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/shop.seller.js');
		
		$templetPath = $this->Module->getPath().'/templets/seller';
		$templetDir = $this->Module->getDir().'/templets/seller';
		
		$IM = $this->IM;
		$Module = $this;

		echo '<div class="ModuleShopSeller">'.PHP_EOL;
		
		if ($this->IM->getModule('member')->isLogged() === false) {
			$this->IM->getWidget('member/login')->setTemplet($templetPath.'/login.php')->doLayout();
		} else {
			$member = $this->IM->getModule('member')->getMember();
			$views = array('item','promotion','order','qna','calculate');
			$icons = array('item'=>'fa-gift','promotion'=>'fa-calendar','order'=>'fa-truck','qna'=>'fa-question-circle','calculate'=>'fa-credit-card');
			
			$view = $this->IM->view == '' ? 'dashboard' : $this->IM->view;
			$viewPanel = $this->getSellerView($view,$views,$icons);
			
			if (file_exists($templetPath.'/index.php') == true) {
				INCLUDE $templetPath.'/index.php';
			}
		}
		
		echo '</div>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		$this->IM->fireEvent('afterGetContext','shop','seller',null,null,$context);
		
		return $context;
	}
	
	function getSellerView($view,$views,$icons) {
		ob_start();
		$this->IM->setView($view);
		$this->IM->addSiteHeader('script',$this->Module->getDir().'/scripts/shop.seller.'.$view.'.js');
		
		$templetPath = $this->Module->getPath().'/templets/seller';
		$templetDir = $this->Module->getDir().'/templets/seller';
		
		$IM = $this->IM;
		$Module = $this;
		
		$this->IM->getModule('wysiwyg')->preload();
		
		if (file_exists($templetPath.'/'.$view.'.php') == true) {
			INCLUDE $templetPath.'/'.$view.'.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		$this->IM->fireEvent('afterGetContext','shop','seller.'.$view,null,null,$context);
		
		return $context;
	}
	
	function getSellerModal($title,$content,$formName='') {
		ob_start();
		
//		$this->IM->removeTemplet();
		
		$templetPath = $this->Module->getPath().'/templets/seller';
		$templetDir = $this->Module->getDir().'/templets/seller';
		
		$IM = $this->IM;
		$Module = $this;
		
		$formName = $formName == '' ? 'ModuleShopSellerModal' : $formName;
		
		echo '<form name="'.$formName.'" onsubmit="return Shop.seller.modal.submit(this);">'.PHP_EOL;
		
		if (file_exists($templetPath.'/modal.php') == true) {
			INCLUDE $templetPath.'/modal.php';
		} else {
			INCLUDE $this->Module->getPath().'/templets/modal.php';
		}
		
		echo '</form>'.PHP_EOL;
		echo '<script>Shop.seller.modal.init("'.$formName.'");</script>'.PHP_EOL;
		
		$context = ob_get_contents();
		ob_end_clean();
		
		
		return $context;
	}
	
	function getSellerItemAddModal() {
		ob_start();
		
		$templetPath = $this->Module->getPath().'/templets/seller';
		$templetDir = $this->Module->getDir().'/templets/seller';
		
		$IM = $this->IM;
		$Module = $this;
		
		$title = $this->getLanguage('seller/item/post/add');
		
		$wysiwyg = $this->IM->getModule('wysiwyg')->setToolBarFixed(false)->setHideButtons(array('formatting','insertcode','video','file','link'))->setName('content')->setModule('shop');
		$wysiwyg->getAttachment()->setWysiwygOnly(true);
		
		if (file_exists($templetPath.'/item.add.php') == true) {
			INCLUDE $templetPath.'/item.add.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $this->getSellerModal($title,$context,'ModuleShopSellerItemAddForm');
	}
	
	function getSellerPromotionAddModal($date) {
		ob_start();
		
		$templetPath = $this->Module->getPath().'/templets/seller';
		$templetDir = $this->Module->getDir().'/templets/seller';
		
		$items = $this->db()->select($this->table->item)->where('midx',$this->IM->getModule('member')->getLogged())->where('status','ACTIVE')->get();
		
		$IM = $this->IM;
		$Module = $this;
		
		$title = $this->getLanguage('seller/promotion/post/add');
		$price = $this->Module->getConfig('promotionPrice');
		$myPoint = $this->IM->getModule('member')->getMember()->point;
		
		
		if (file_exists($templetPath.'/promotion.add.php') == true) {
			INCLUDE $templetPath.'/promotion.add.php';
		}
		
		$context = ob_get_contents();
		ob_end_clean();
		
		return $this->getSellerModal($title,$context,'ModuleShopSellerPromotionAddForm');
	}
	
	function encodeContent($content,$attachment) {
		if (preg_match_all('/<img(.*?)data-idx="([0-9]+)"(.*?)>/',$content,$match) == true) {
			for ($i=0, $loop=count($match[0]);$i<$loop;$i++) {
				if (in_array($match[2][$i],$attachment) == true) {
					$image = preg_replace('/ src="(.*?)"/','',$match[0][$i]);
					$content = str_replace($match[0][$i],$image,$content);
				} else {
					$file = $this->db()->select($this->table->attachment)->where('idx',$match[2][$i])->getOne();
					if ($file == null) {
						$content = str_replace($match[0][$i],'',$content);
					} else {
						$fileIdx = $this->IM->getModule('attachment')->copyFile($match[2][$i]);
						$image = preg_replace('/ src="(.*?)"/','',$match[0][$i]);
						$image = str_replace('data-idx="'.$match[2][$i].'"','data-idx="'.$fileIdx.'"',$image);
						$content = str_replace($match[0][$i],'',$content);
					}
				}
			}
		}
		
		return $content;
	}
	
	function doProcess($action) {
		$results = new stdClass();
		$values = new stdClass();
		
		if ($action == 'getCategory') {
			$parent = Request('parent');
			$depth = Request('depth');
			
			if ($parent) $values->categorys = $this->db()->select($this->table->category)->where('parent',$parent)->orderBy('sort','asc')->get();
			elseif ($depth) $values->categorys = $this->db()->select($this->table->category)->where('depth',$depth)->orderBy('sort','asc')->get();
			
			if ($parent == 0) $results->parent = null;
			else $results->parent = $this->db()->select($this->table->category)->where('idx',$parent)->getOne();
			
			$results->success = true;
			$results->categorys = $values->categorys;
		}
		
		if ($action == 'sellerItem') {
			$lists = $this->db()->select($this->table->item)->where('midx',$this->IM->getModule('member')->getLogged());
			$total = $lists->copy()->count();
			$lists = $lists->get();
			
			for ($i=0, $loop=count($lists);$i<$loop;$i++) {
				$lists[$i]->image = $this->IM->getModule('attachment')->getAttachmentUrl($lists[$i]->image_default);
			}
			
			$results->success = true;
			$results->total = $total;
			$results->lists = $lists;
		}
		
		if ($action == 'sellerItemAddModal') {
			$results->success = true;
			$results->modalHtml = $this->getSellerItemAddModal();
		}
		
		if ($action == 'sellerItemImage') {
			$meta = json_decode($_SERVER['HTTP_IMAGE_META']);
			
			$fileBytes = file_get_contents("php://input");
			$tempFileName = $this->IM->getModule('attachment')->getTempPath(true).'/'.md5($fileBytes).'.'.rand(100000,999999);
			file_put_contents($tempFileName,$fileBytes);
			if ($this->IM->getModule('attachment')->createThumbnail($tempFileName,$tempFileName,600,600,false,'jpg') == true) {
				if ($meta->imageType == 'addition' || empty($meta->imageIdx) == true || Decoder($meta->imageIdx) == false) {
					$imageIdx = $this->IM->getModule('attachment')->fileSave($meta->imageType.'.jpg',$tempFileName,'shop',$meta->imageType);
				} else {
					$imageIdx = $this->IM->getModule('attachment')->fileReplace($post->logo,$meta->imageType.'.jpg',$tempFileName);
				}
				
				$results->success = true;
				$results->imageIdx = Encoder($imageIdx);
				$results->imageType = $meta->imageType;
				$results->imageUrl = $this->IM->getModule('attachment')->getAttachmentUrl($imageIdx,$meta->imageType == 'default' ? 'view' : 'thumbnail');
			}
		}
		
		if ($action == 'sellerItemPost') {
//			print_r($_REQUEST);
			$errors = array();
			$values->idx = Request('idx');
			$values->title = Request('title') ? Request('title') : $errors['title'] = $this->getLanguage('seller/item/post/help/title/error');
			$values->detail = Request('detail');
			$values->seller = Request('seller') ? Request('seller') : $errors['seller'] = $this->getLanguage('seller/item/post/help/seller/error');
			$values->homepage = Request('homepage') ? (preg_match('/^http/',Request('homepage')) == true ? Request('homepage') : 'http://'.Request('homepage')) : '';
			$values->category1 = Request('category1') ? Request('category1') : $errors['category1'] = $this->getLanguage('seller/item/post/help/category/error');
			$values->category2 = Request('category2') ? Request('category2') : 0;
			$values->category3 = Request('category3') ? Request('category3') : 0;
			
			$values->brand = Request('brand');
			$values->maker = Request('maker');
			$values->model = Request('model');
			$values->price = preg_match('/^[1-9]+[0-9]*/',Request('price')) == true ? Request('price') : $errors['price'] = $this->getLanguage('seller/item/post/help/price/error');
			$values->allow_youth = Request('allow_youth');
			$values->image_default = Request('image_default') && Decoder(Request('image_default')) !== false ? Decoder(Request('image_default')) : $errors['image_default'] = $this->getLanguage('seller/item/post/help/image/error');
			$values->image_addition = Request('image_addition') ? json_decode(Request('image_addition')) : array();
			
			$values->content = Request('content') ? Request('content') : $values->errors['content'] = $this->getLanguage('seller/item/post/help/content/error');
			
			$values->attachments = is_array(Request('attachments')) == true ? Request('attachments') : array();
			for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
				$values->attachments[$i] = Decoder($values->attachments[$i]);
			}
			
			$values->content = $this->encodeContent($values->content,$values->attachments);
			
			for ($i=0, $loop=count($values->image_addition);$i<$loop;$i++) {
				$values->image_addition[$i] = Decoder($values->image_addition[$i]);
			}
			
			$values->option_enable = Request('option_enable') == 'TRUE' ? true : false;
			if ($values->option_enable == true) {
				$values->options = json_decode(Request('options'));
				if (count($values->options->names) == 0 || count($values->options->selects) == 0) {
					$errors['options'] = $this->getLanguage('seller/item/post/help/options/error');
				}
				
				for ($i=0, $loop=count($values->options->selects);$i<$loop;$i++) {
					if (is_numeric($values->options->selects[$i]->ea) == false || is_numeric($values->options->selects[$i]->price) == false) {
						$errors['options'] = $this->getLanguage('seller/item/post/help/options/numberOnly');
						break;
					}
				}
			} else {
				$values->ea = strlen(Request('ea')) > 0 ? Request('ea') : -1;
				$values->options = new stdClass();
				$values->options->names = '';
				$values->options->selects = array();
			}
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notLogged');
			} elseif (count($errors) == 0) {
				$insert = array();
				$insert['title'] = $values->title;
				$insert['detail'] = $values->detail;
				$insert['seller'] = $values->seller;
				$insert['homepage'] = $values->homepage;
				$insert['category1'] = $values->category1;
				$insert['category2'] = $values->category2;
				$insert['category3'] = $values->category3;
				$insert['brand'] = $values->brand;
				$insert['maker'] = $values->maker;
				$insert['model'] = $values->model;
				$insert['price'] = $values->price;
				$insert['allow_youth'] = $values->allow_youth;
				$insert['content'] = $values->content;
				$insert['search'] = GetString($values->content,'index');
				$insert['image_default'] = $values->image_default;
				$insert['options'] = json_encode($values->options->names,JSON_UNESCAPED_UNICODE);
				
				if ($values->idx == null) {
					$insert['midx'] = $this->IM->getModule('member')->getLogged();
					$insert['reg_date'] = time();
					
					$values->idx = $this->db()->insert($this->table->item,$insert)->execute();
					$results->success = true;
				}
				
				if ($results->success == true) {
					for ($i=0, $loop=count($values->attachments);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->attachments[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->attachments[$i],'type'=>'ITEMDETAIL','parent'=>$values->idx))->execute();
						}
					}
					
					for ($i=0, $loop=count($values->image_addition);$i<$loop;$i++) {
						if ($this->db()->select($this->table->attachment)->where('idx',$values->image_addition[$i])->count() == 0) {
							$this->db()->insert($this->table->attachment,array('idx'=>$values->image_addition[$i],'type'=>'ITEM','parent'=>$values->idx))->execute();
						}
					}
					
					$this->db()->delete($this->table->item_option)->where('idx',$values->idx)->execute();
					
					if ($values->option_enable == true) {
						for ($i=0, $loop=count($values->options->selects);$i<$loop;$i++) {
							$option = array();
							$option['idx'] = $values->idx;
							$option['option1'] = $values->options->selects[$i]->option1;
							$option['option2'] = $values->options->selects[$i]->option2 == null ? '' : $values->options->selects[$i]->option2;
							$option['option3'] = $values->options->selects[$i]->option3 == null ? '' : $values->options->selects[$i]->option3;
							$option['price'] = $values->options->selects[$i]->price;
							$option['ea'] = $values->options->selects[$i]->ea;
							$option['sort'] = $i;
							$this->db()->insert($this->table->item_option,$option)->execute();
						}
					}
				}
				
			} else {
				$results->success = false;
				$results->errors = $errors;
				$results->message = $this->getLanguage('error/required');
			}
		}
		
		if ($action == 'sellerPromotion') {
			$date = Request('date') ? strtotime(Request('date')) : strtotime(date('Y-m-d'));
			$timeInterval = $this->Module->getConfig('promotionTimeInterval') ? $this->Module->getConfig('promotionTimeInterval') : 30;
			
			$startDate = $date - 60 * 60 * 24 * 3;
			$endDate = $date + 60 * 60 * 24 * 4;
			
			$registeredLists = array();
			$promotions = $this->db()->select($this->table->promotion)->where('start_date',$startDate,'>=')->where('start_date',$endDate,'<')->get();
			for ($i=0, $loop=count($promotions);$i<$loop;$i++) {
				if (empty($registeredLists[$promotions[$i]->start_date]) == true) $registeredLists[$promotions[$i]->start_date] = array();
				$registeredLists[$promotions[$i]->start_date][] = $promotions[$i]->midx;
			}
			
			$midx = $this->IM->getModule('member')->getLogged();
			$lists = array();
			for ($i=0;$i<1440;$i=$i+$timeInterval) {
				$list = array();
				
				$list['start_time'] = $startDate + $i * 60;
				$list['start_date'] = date('Y-m-d',$startDate + $i * 60);
				
				for ($j=1;$j<=7;$j++) {
					$time = $startDate + $i * 60 + 86400 * ($j - 1);
					$list['day'.$j.'_time'] = $time;
					$list['day'.$j.'_date'] = date('Y-m-d',$time);
					
					if (isset($registeredLists[$time]) == true && count($registeredLists[$time]) > 0) {
						$list['day'.$j] = count($registeredLists[$time]) >= $this->Module->getConfig('promotionTimeItemLimit') ? 'FULL' : 'EMPTY';
						$registeredMidx = array_count_values($registeredLists[$time]);
						$list['day'.$j].= '@';
						$list['day'.$j].= isset($registeredMidx[$midx]) == true ? $registeredMidx[$midx] : 0;
					} else {
						$list['day'.$j] = 'EMPTY@0';
					}
				}
				
				$lists[] = $list;
			}
			
			$results->success = true;
			$results->total = count($lists);
			$results->lists = $lists;
		}
		
		if ($action == 'sellerPromotionAddModal') {
			$item = $this->db()->select($this->table->item)->where('midx',$this->IM->getModule('member')->getLogged())->where('status','ACTIVE')->count();
			
			if ($item == 0) {
				$results->success = false;
				$results->message = $this->getLanguage('error/itemNotFound');
			} else {
				$date = Request('date');
				if ($date < time()) {
					$results->success = false;
					$results->message = $this->getLanguage('error/pastTime');
				} else {
					$results->success = true;
					$results->modalHtml = $this->getSellerPromotionAddModal($date);
				}
			}
		}
		
		if ($action == 'sellerPromotionPost') {
			$errors = array();
			$values->date = Request('date');
			$values->item = Request('item');
			$values->min = Request('min');
			$values->max = Request('max');
			$values->ea = preg_match('/^[1-9]+[0-9]*$/',Request('ea')) == true ? Request('ea') : $errors['ea'] = $this->getLanguage('seller/promotion/post/help/ea/error');
			
			$timeInterval = $this->Module->getConfig('promotionTimeInterval') ? $this->Module->getConfig('promotionTimeInterval') : 30;
			$price = $this->Module->getConfig('promotionPrice');
			if ($values->date < time() || $values->date % ($timeInterval * 60) != 0) $errors['date'] = $this->getLanguage('seller/promotion/post/help/date/error');
			
			$check = $this->db()->select($this->table->promotion)->where('start_date',$values->date)->count();
			if ($check >= $this->Module->getConfig('promotionTimeItemLimit')) $errors['date'] = $this->getLanguage('seller/promotion/post/help/date/duplicated');
			
			$check = $this->db()->select($this->table->item)->where('idx',$values->item)->where('midx',$this->IM->getModule('member')->getLogged())->where('status','ACTIVE')->get();
			if ($check == null) $errors['item'] = $this->getLanguage('seller/promotion/post/help/item/error');
			
			if ($this->IM->getModule('member')->isLogged() == false) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notFound');
			} elseif ($this->IM->getModule('member')->getMember()->point < $price) {
				$results->success = false;
				$results->message = $this->getLanguage('error/notEnoughPoint');
			} elseif (count($errors) == 0) {
				$insert = array();
				$insert['midx'] = $this->IM->getModule('member')->getLogged();
				$insert['item'] = $values->item;
				$insert['start_date'] = $values->date;
				$insert['end_date'] = $values->date + 60 * $timeInterval;
				$insert['min'] = $values->min;
				$insert['max'] = $values->max;
				$insert['ea'] = $values->ea;
				$insert['reg_date'] = time();
				
				$values->idx = $this->db()->insert($this->table->promotion,$insert)->execute();
				
				$this->IM->getModule('member')->sendPoint(null,$price * -1,'shop','promotion',array('idx'=>$values->idx));
//				$this->IM->getModule('member')->addActivity(null,$values->dataroom->post_exp,'dataroom','post',array('idx'=>$values->idx));

				$results->success = true;
			} else {
				$results->success = false;
				$results->errors = $errors;
				if (isset($errors['date']) == true) $results->message = $errors['date'];
				else $results->message = $this->getLanguage('error/required');
			}
		}
		
		$this->IM->fireEvent('afterDoProcess','shop',$action,$values,$results);
		
		return $results;
	}
}
?>