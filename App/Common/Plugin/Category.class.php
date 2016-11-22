<?php
namespace Common\Plugin;
	class Category{
	//无限分类（组合一维数组）
		static public function unlimitcategory($cate,$html="--",$pid="0",$level="0"){
			$arr= array();
			foreach ($cate as $v) {
				if($v['pid']==$pid){
					$v['level']=$level+1;
					$v['html']=str_repeat($html,$level);
					$arr[]=$v;
					$arr=array_merge($arr,self::unlimitcategory($cate,$html,$v['id'],$level+1));
				}
			}
			return $arr;
		}
		//查出所有子类，并把子类组成数组（组合多维数组）
		static public function unlimitlayer($cate,$pid=0){
			$arr=array();
			foreach ($cate as $v) {
				if($v['pid']==$pid){
					$v['child']=self::unlimitlayer($cate,$v['id']);
					$arr[]=$v;
				}
			}
			return $arr;
		}
		//通过子级，查出所有父级和本身
		static public function unlimitparent($cate,$id){
			$arr=array();
			foreach ($cate as $v) {
				if($v['id']==$id){
					$arr[]=$v;
					$arr=array_merge(self::unlimitparent($cate,$v['pid']),$arr);
				}
			}
			return $arr;
		}
		//知道父级，查询所有子级
		static public function unlimitchildren($cate,$pid){
			$arr=array();
			foreach ($cate as $v) {
				if($v['pid']==$pid){
					$arr[]=$v['id'];
					$arr=array_merge($arr,self::unlimitchildren($cate,$v['id']));
				}
			}
			return $arr;
		}

	}
?>