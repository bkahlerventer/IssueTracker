<?php
/**
 * Wrapper function for getfield to retrieve Category name
 *
 * @param integer $cid ID of category to get
 * @returns string $category
 */
function category($cid)
{
  global $dbi,$categories_category_cache,$cache_data;

  if (empty($cid)) {
    return "&nbsp;";
  }

  if (is_array($categories_category_cache)
  and in_array("categories",$cache_data)) {
    if (array_key_exists($cid,$categories_category_cache)) {
      return $categories_category_cache[$cid];
    }
  } 
 
  return getfield("categories","category","cid",$cid);
}

/* {{{ Function: product */
/**
 * Wrapper function for getfield to retrieve Product name
 *
 * @param integer $pid ID of product
 * @returns string
 */
function product($pid)
{
  global $dbi,$products_product_cache,$cache_data;

  if (empty($pid)) {
    return "&nbsp;";
  }

  if (is_array($products_product_cache)
  and in_array("products",$cache_data)) {
    if (array_key_exists($pid,$products_product_cache)) {
      return $products_product_cache[$pid];
    }
  }

  return getfield("products","product","pid",$pid);
}
/* }}} */

/* {{{ Function: status */
/**
 * Wrapper function for getfield to retrieve Status name
 *
 * @param integer $sid ID of status to get
 * @returns string $status
 */
function status($sid)
{
  global $dbi,$statuses_status_cache,$cache_data;

  if (empty($sid)) {
    return "&nbsp;";
  }

  if (is_array($statuses_status_cache)
  and in_array("statuses",$cache_data)) {
    if (array_key_exists($sid,$statuses_status_cache)) {
      return $statuses_status_cache[$sid];
    }
  }
  
  return getfield("statuses","status","sid",$sid);
}
/* }}} */

/* {{{ Function: status_type */
/**
 * Retrieve a status' type
 *
 * @param integer $sid ID of status
 * @return integer
 */
function status_type($sid)
{
  global $dbi;

  $sql  = "SELECT status_type ";
  $sql .= "FROM statuses ";
  $sql .= "WHERE sid='$sid'";
  $result = $dbi->query($sql);
  if ($dbi->num_rows($result) > 0) {
    list($type) = $dbi->fetch($result);
    $dbi->free($result);
    return $type;
  }
}
/* }}} */

/* {{{ Function: fetch_status */
/**
 * Retrieve an array of statuses matching given type
 *
 * @param integer $type Status Type (see conf/const.php for status type constants)
 * @return array
 */
function fetch_status($type)
{
  global $dbi;

  $statuses = array();

  if (!is_array($_ENV['stype'])) {
    $_ENV['stype'] = array();
  }

  if (is_array($type)) {
    foreach ($type as $val) {
      $s = fetch_status($val);
      foreach ($s as $sid) {
        if (!in_array($sid,$statuses)) {
          array_push($statuses,$sid);
        }
      }
    }
  } else {
    if (array_key_exists($type,$_ENV['stype'])) {
      return $_ENV['stype'][$type];
    } else {
      $sql  = "SELECT sid ";
      $sql .= "FROM statuses ";
      $sql .= "WHERE status_type='$type'";
      $result = $dbi->query($sql);
      if ($dbi->num_rows($result) > 0) {
        while (list($sid) = $dbi->fetch($result)) {
          if (!in_array($sid,$statuses)) {
            array_push($statuses,$sid);
          }
        }
        $dbi->free($result);
      }
    }

    $_ENV['stype'][$type] = $statuses;
    return $statuses;
  }

  return $statuses;
}
/* }}} */
?>
