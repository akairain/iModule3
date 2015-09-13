<?php
$categorys = array(
	'none'=>'선택안함',
	'wear'=>'의류',
	'shoes'=>'구두/신발',
	'bag'=>'가방',
	'fashion'=>'패션잡화(모자/벨트/액세서리)',
	'bedding'=>'침구류/커튼',
	'furniture'=>'가구(침대/소파/싱크대/DIY제품)',
	'image_appliances'=>'영상가전(TV류)',
	'home_appliances'=>'가정용전기제품(냉장고/세탁기/식기세척기/전자레인지)',
	'season_appliances'=>'계절가전(에어컨/온풍기)',
	'office_appliances'=>'사무용기기(컴퓨터/노트북/프린터)',
	'optics_appliances'=>'광학기기(디지털카메라/캠코더)',
	'microelectronics'=>'소형전자(MP3/전자사전등)',
	'mobile'=>'휴대폰',
	'navigation'=>'네비게이션',
	'car'=>'자동차용품(자동차부품/기타자동차용품)',
	'medical'=>'의료기기',
	'kitchenware'=>'주방용품',
	'cosmetics'=>'화장품',
	'jewelry'=>'귀금속/보석/시계류',
	'food'=>'식품(농수산물)',
	'general_food'=>'가공식품',
	'diet_food'=>'건강기능식품',
	'kids'=>'영유아용품',
	'instrument'=>'악기',
	'sports'=>'스포츠용품',
	'books'=>'서적',
	'reserve'=>'호텔/펜션예약',
	'travel'=>'여행패키지',
	'airline_ticket'=>'항공권',
	'rent_car'=>'자동차대여서비스(렌터카)',
	'rental_water'=>'물품대여서비스(정수기,비데,공기청정기 등)',
	'rental_etc'=>'물품대여서비스(서적,유아용품,행사용품 등)',
	'digital_contents'=>'디지털콘텐츠(음원,게임,인터넷강의 등)',
	'gift_card'=>'상품권/쿠폰',
	'etc'=>'기타'
);

$fields = array();
$fields['wear'] = array(
	// '필드키'=>array('필드제목','안내문구','기본값','필수여부','첨부파일 여부')
	'material'=>array('제품소재','섬유의 조성 또는 혼용률을 백분율로 표시, 기능성인 경우 성적서 또는 허가서','상품 상세페이지 참조',true,true),
	'color'=>array('색상','','상품 상세페이지 참조',true,true)
);
$fields['shoes'] = array(
	'material'=>array('제품소재','섬유의 조성 또는 혼용률을 백분율로 표시, 기능성인 경우 성적서 또는 허가서','상품 상세페이지 참조',true,true)
);
?>