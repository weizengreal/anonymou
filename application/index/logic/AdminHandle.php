<?php
/**
 * Created by PhpStorm.
 * User: zhengwei
 * Date: 2017/5/22
 * Time: 下午9:35
 */
namespace app\index\logic;

use think\Db;

class AdminHandle {
    /*
     * 根据已经上传的excel文件将数据导入到mysql中
     * */
    public static function excelToMysql($mediaid,$filename) {
        $filepath = ROOT_PATH.'public'.DS.'excel'.DS.$filename;
        if (!file_exists($filepath)) {
            return array(
                'status'=>-1,
                'info'=>'未能找到该文件'
            );
        }
        $reader = \PHPExcel_IOFactory::createReader('Excel5'); //设置以Excel5格式(Excel97-2003工作簿)
        $PHPExcel = $reader->load($filepath); // 载入excel文件
        $sheet = $PHPExcel->getSheet(0); // 读取第一個工作表
        $highestRow = $sheet->getHighestRow(); // 取得总行数
        $subTime = time();
        $insertArr = array();
        for ($row = 2; $row <= $highestRow && $row<=500; $row++) { //行数是以第1行开始
            $it = array();
            $it['name'] = $sheet->getCell('A'.$row)->getValue();
            $it['lessons'] = self::getLessonStr($sheet->getCell('B'.$row)->getValue());
            $it['detail'] = $sheet->getCell('C'.$row)->getValue();
            $headimg = $sheet->getCell('D'.$row)->getValue();
            $headimg = is_numeric($headimg) && $headimg > 0 && $headimg <=20 ? $headimg : 1;
            $it['headimg'] = (int)$headimg ;
            $it['subtime'] = $subTime;
            $it['show'] = 1;
            $it['mediaid'] = $mediaid;
            $insertArr[] = $it;
        }
        // 不论结果如何，该excel必须删除，因为腾讯云很抠，硬盘太小了
        unlink($filepath);
        return array(
            'status'=>Db::table('anony_teacher')->insertAll($insertArr) != false ? 1 : 2,
        );
    }

    private static function getLessonStr($lessonStr) {
        $res = array();
        $lessonArr = explode('|',$lessonStr);
        foreach ($lessonArr as $item) {
            if(! empty($item) ) {
                $res[] = $item;
            }
        }
        return json_encode($res,JSON_UNESCAPED_UNICODE);
    }

}
