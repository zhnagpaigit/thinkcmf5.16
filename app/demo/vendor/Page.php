<?php
/**
 * Created by PhpStorm.
 * User: zw
 * Date: 2021/3/5
 * Time: 14:18
 */
namespace app\demo\vendor;
class Page{
  public $totalPage;
  public $pageLen=20;
  public $args;
  public $url;
  public $page =1;
  protected $href;

  public function __construct($totalPage=0,$pageLen=20,$url='',$args='',$page=1)
  {
      $this->totalPage = $totalPage;
      $this->pageLen = $pageLen;
      $this->url = $url;
      $this->args = $args;
      $this->page = $page;

  }

  public function setVal($name,$val)
  {
      $this[$name] = $val;
  }


  public function show()
  {
      $totalPage = $this->totalPage;
      $pageLen = $this->pageLen;
      $page = $this->page;
      $args = $this->args;
      $url = $this->url;



      if(!empty($args))
      {
          if(is_array($args))
          {
              $args = http_build_query($args);
          }
          $url .= "?".$args;
      }
      else
      {
          $url .= "?1=1";
      }

      $this->href = $url;
      //-- 只有一页数据，无须分页；
      if($totalPage <0) return '';
       //小于12页 -- 直接显示，无需 上下翻页按钮
      elseif($totalPage<14)
      {
         $arr = $this->noPageTurn($totalPage,$page);
      }
      else
      {
          $arr = $this->pageTurn($totalPage,$page);
      }



        $arr = $this->assigns($arr);
      dump($arr);
        return implode(' ',$arr);

  }

    //小于14页 -- 直接显示，无需 上下翻页按钮
    private function noPageTurn($totalPage,$page)
    {
        $page = ($page>$totalPage)?$totalPage:$page;
        $arr =[];
        for($i=1;$i<=$totalPage;$i++)
        {

            $arr[] = $i;

        }

       return $arr;
    }

    private function PageTurn($totalPage,$page)
    {
        $arr =[];
        $arr[] = 0;
        $page = ($page>$totalPage)?$totalPage:$page;

        if($page<5)
        {
            for($i=1;$i<6;$i++)
            {
                $arr[] = $i;
            }
            $arr[]=-2;
            $arr[]=$totalPage-1;
            $arr[]=$totalPage;
        }
        else
        {
            $arr[] =1;
            $arr[] =2;
            $arr[]=-2;
            if($page<$totalPage-4)
            {


                for($i=1;$i<=5;$i++)
                {
                    $arr[] = $page-(3-$i);
                }
                $arr[]=-2;
                $arr[] = $totalPage -1;
                $arr[] = $totalPage;
            }
            else
            {
                for($i=1;$i<=6;$i++)
                {
                    $arr[] = $totalPage-(6-$i);
                }
            }
        }




        $arr[] = -1;
        return $arr;
    }

    //组装
    private function assigns($arr)
    {
        if(!is_array($arr)) return '';
        $temp =[];
        foreach ($arr as $val)
        {
            $temp[] = $this->assign($val);
        }
        return $temp;
    }

    private function assign($val)
    {
        $page = $this->page;
        $totalPage = $this->totalPage;
        $href = $this->href;
        $li = "";
        switch ($val)
        {
            case 0://<<
                if($page==1)
                {
                    $li= "<li class='disabled'> <span> << </span> </li>";
                }
                else
                {
                    $href .= "&page=".($page-1);
                    $li= "<li><a href='{$href}'><span> << </span></a> </li>";
                }

                break;
            case -1://>>
                if($page==$totalPage)
                {
                    $li= "<li class='disabled'> <span> << </span> </li>";
                }
                else
                {
                    $href .= "&page=".($page+1);
                    $li= "<li><a href='{$href}'><span>>> </span> </a> </li>";
                }
                break;
            case -2:// ...
                $li= "<li class='disabled'>...</li>";
                break;
            default:
                if($val == $page)
                {
                    $li= "<li class='active'><span>{$page}</span></li>";
                }
                else
                {
                    $href .= "&page=".$val;
                    $li= "<li ><a href='{$href}'><span>{$val}</span></a></li>";
                }
        }
        return $li;
    }



}