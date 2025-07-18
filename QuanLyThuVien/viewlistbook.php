<?php 
require_once("incfiles/head.php");
$idtype = isset($_GET['id']) ? (int)$_GET['id'] : 0; // Ép kiểu để tránh SQL Injection
?>
<br>
<script type="text/javascript">
	function reload(){
	  	window.location.reload();
	}
	function editbook(id){
		$('#loadeditbook'+id).html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Đang kiểm tra');
		setTimeout(function(){
			$('#check'+id).load('editbook.php',$('#form-editbook'+id).serializeArray());
			$('#loadeditbook'+id).html('<i class="fa fa-check-square-o" aria-hidden="true"></i> Save');
		},1000);
	}
	function addbook(){
		$('#loadaddbook').html('<i class="fa fa-spinner fa-pulse fa-fw"></i> Đang kiểm tra');
		setTimeout(function(){
			$('#check2').load('addbook.php',$('#form-addbook').serializeArray());
			$('#loadaddbook').html('<i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm');
		},1000);
	}
</script>
<div class="container-fluid">
	<div class="row">
		<div class="col-md-9">
<?php 
$query = "SELECT * FROM theloai WHERE ID = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "i", $idtype);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$title2 = mysqli_fetch_array($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
?>
<button class="btn btn-success">Danh Mục: <?php echo htmlspecialchars($title2['name'] ?? 'Không tìm thấy'); ?> </button>
			<table class="table table-hover">
			    <thead>
			      <tr>
			        <th>ID</th>
			        <th>Tên sách</th>
			        <th>Tác giả</th>
			        <th>Số lượng</th>
			        <th>Admin</th>
			      </tr>
			    </thead>
			    <tbody>
			    <?php 
				$query = "SELECT * FROM dsbook WHERE theloai = ?";
				$stmt = mysqli_prepare($conn, $query);
				mysqli_stmt_bind_param($stmt, "i", $idtype);
				mysqli_stmt_execute($stmt);
				$result = mysqli_stmt_get_result($stmt);
				while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
				?>
			     <tr>
			        <td><?php echo htmlspecialchars($row['IDbook']); ?></td>
			        <td><?php echo htmlspecialchars($row['namebook']); ?></td>
			        <td><?php echo htmlspecialchars($row['tacgia']); ?></td>
			        <td><?php echo htmlspecialchars($row['soluong']); ?></td>
			        <td>
			        <a href="delbook.php?idbook=<?php echo urlencode($row['IDbook']); ?>&idtype=<?php echo urlencode($row['theloai']); ?>"><i style="color:red; margin-right: 10px;" class="fa fa-times-circle" aria-hidden="true"></i></a>
					<a href="" data-toggle="modal" data-target="#myModal<?php echo $row['IDbook']; ?>">
					  <i class="fa fa-pencil-square-o" aria-hidden="true"></i>
					</a>
					<div class="modal fade" id="myModal<?php echo $row['IDbook']; ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
					  <div class="modal-dialog" role="document">
					    <div class="modal-content">
					      <div class="modal-header">
					        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
					        <h4 class="modal-title" id="myModalLabel">Chỉnh sửa sách</h4>
					      </div>
					      <div class="modal-body">
							   <form id="form-editbook<?php echo $row['IDbook']; ?>" method="POST">
						  		<div class="form-group">
						  		<input class="form-control" name="idbook" id="idtype" value="<?php echo htmlspecialchars($row['IDbook']); ?>" type="hidden">
								    <div class="input-group">
								      <div class="input-group-addon">Tên sách</div>
								      <input type="text" name="tensach" class="form-control" id="exampleInputAmount" placeholder="Nhập tên sách" value="<?php echo htmlspecialchars($row['namebook']); ?>">
								    </div>
								</div>
								<div class="form-group">
								    <div class="input-group">
								      <div class="input-group-addon">Số lượng</div>
								      <input type="text" name="soluong" class="form-control" id="exampleInputAmount" placeholder="" value="<?php echo htmlspecialchars($row['soluong']); ?>">
								    </div>
								</div>
								<div class="form-group">
								    <div class="input-group">
								      <div class="input-group-addon">Tác giả</div>
								      <input type="text" name="tacgia" class="form-control" id="exampleInputAmount" placeholder="Nhập tên tác giả" value="<?php echo htmlspecialchars($row['tacgia']); ?>">
								    </div>
								</div>
								<div class="form-group">
								  <label for="sel1">Thể loại:</label>
								  <select class="form-control" name="theloai" id="sel1">
								   <?php 
									$query2 = "SELECT * FROM theloai";
									$result2 = mysqli_query($conn, $query2);
									while ($row2 = mysqli_fetch_array($result2, MYSQLI_ASSOC)) {
										echo '<option value="' . $row2['ID'] . '"' . ($row2['ID'] == $row['theloai'] ? ' selected="selected"' : '') . '>' . htmlspecialchars($row2['name']) . '</option>';
									}
									?>
								  </select>
								</div>
							</form>
							<div id="check<?php echo $row['IDbook']; ?>"></div>
					      </div>
					      <div class="modal-footer">
					        <button onclick="reload();" type="button" class="btn btn-default" data-dismiss="modal">Close</button>
							<button id="btn-edittype" onclick="editbook(<?php echo $row['IDbook']; ?>);" type="button" class="btn btn-primary">
					        	<div id="loadeditbook<?php echo $row['IDbook']; ?>"><i class="fa fa-check-square-o" aria-hidden="true"></i> Save</div>
					        </button>
					      </div>
					    </div>
					  </div>
					</div>
				</td>
			      </tr>
			     <?php } 
				 mysqli_free_result($result);
				 ?>
			    </tbody>
			</table>
<a href="/index.php"> <button class="btn btn-success">Về Trang Chủ</button></a>
		</div>
		<div class="col-md-3">
			<div class="panel panel-primary">
				  <div class="panel-heading">Thêm Sách</div>
				  <div class="panel-body">
				  <form action="addbook.php" id="form-addbook" method="POST">
				  		<div class="form-group">
						    <div class="input-group">
						      <div class="input-group-addon">Tên sách</div>
						      <input type="text" name="tensach" class="form-control" id="exampleInputAmount" placeholder="">
						    </div>
						</div>
						<div class="form-group">
						    <div class="input-group">
						      <div class="input-group-addon">Số lượng</div>
						      <input type="text" name="soluong" class="form-control" id="exampleInputAmount" placeholder="">
						    </div>
						</div>
						<div class="form-group">
						    <div class="input-group">
						      <div class="input-group-addon">Tác giả</div>
						      <input type="text" name="tacgia" class="form-control" id="exampleInputAmount" placeholder="">
						    </div>
						</div>
						<div class="form-group">
						  <label for="sel1">Thể loại:</label>
						  <select class="form-control" name="theloai" id="sel1">
						   <?php 
							$query = "SELECT * FROM theloai";
							$result = mysqli_query($conn, $query);
							while ($row = mysqli_fetch_array($result, MYSQLI_ASSOC)) {
								echo '<option value="' . $row['ID'] . '">' . htmlspecialchars($row['name']) . '</option>';
							}
							mysqli_free_result($result);
							?>
						  </select>
						</div>
					</form>
					<center>
						<button onclick="addbook();" name="btn-reg" class="btn btn-info"><div id="loadaddbook"><i class="fa fa-check-square-o" aria-hidden="true"></i> Thêm Sách </div> </button><br>
						<font color="red" id="check2"></font>
					</center>
				  </div>
			</div>
		</div>
	</div>
</div>
  <!-- Footer -->
    <footer class="text-center">
        <div class="footer-above">
            <div class="container">
                <div class="row">
                    <div class="footer-col col-md-4">
                        <h3>Địa chỉ</h3>
                        <p>Trường CNTT-TT
                            <br>Đại Học Cần Thơ</p>
                    </div>
                    <div class="footer-col col-md-4">
                        <h3>Theo dõi Trên Mạng Xã Hội</h3>
                        <ul class="list-inline">
                            <li>
                                <a href="http://facebook.com/tnit97" class="btn-social btn-outline"><i class="fa fa-fw fa-facebook"></i></a>
                            </li>
                            <li>
                                <a href="https://plus.google.com/113033670997906755955" class="btn-social btn-outline"><i class="fa fa-fw fa-google-plus"></i></a>
                            </li>
                            
                            <li>
                                <a href="http://thanhtrungit.com" class="btn-social btn-outline"><i class="fa fa-fw fa-dribbble"></i></a>
                            </li>
                        </ul>
                    </div>
                    <div class="footer-col col-md-4">
                        <h3>Thông Tin</h3>
                        <p>Trương Minh Dĩ - Lê Hoàng Duy </p>
                        <p>Lập trình viên - web designer </p>
                        <p> Website: <a href="http://quanlithuvien.localhost">quanlithuvien.localhost</a> </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="footer-below">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        Copyright &copy; 2025 
                    </div>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scroll to Top Button (Only visible on small and extra-small screen sizes) -->
    <div class="scroll-top page-scroll hidden-sm hidden-xs hidden-lg hidden-md">
        <a class="btn btn-primary" href="#page-top">
            <i class="fa fa-chevron-up"></i>
        </a>
    </div>

    <!-- Portfolio Modals -->
    <div class="portfolio-modal modal fade" id="portfolioModal1" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/cabin.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portfolio-modal modal fade" id="portfolioModal2" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/cake.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portfolio-modal modal fade" id="portfolioModal3" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/circus.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portfolio-modal modal fade" id="portfolioModal4" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/game.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portfolio-modal modal fade" id="portfolioModal5" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/safe.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="portfolio-modal modal fade" id="portfolioModal6" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-content">
            <div class="close-modal" data-dismiss="modal">
                <div class="lr">
                    <div class="rl">
                    </div>
                </div>
            </div>
            <div class="container">
                <div class="row">
                    <div class="col-lg-8 col-lg-offset-2">
                        <div class="modal-body">
                            <h2>Project Title</h2>
                            <hr class="star-primary">
                            <img src="img/portfolio/submarine.png" class="img-responsive img-centered" alt="">
                            <p>Use this area of the page to describe your project. The icon above is part of a free icon set by <a href="https://sellfy.com/p/8Q9P/jV3VZ/">Flat Icons</a>. On their website, you can download their free set with 16 icons, or you can purchase the entire set with 146 icons for only $12!</p>
                            <ul class="list-inline item-details">
                                <li>Client:
                                    <strong><a href="http://startbootstrap.com">Start Bootstrap</a>
                                    </strong>
                                </li>
                                <li>Date:
                                    <strong><a href="http://startbootstrap.com">April 2014</a>
                                    </strong>
                                </li>
                                <li>Service:
                                    <strong><a href="http://startbootstrap.com">Web Development</a>
                                    </strong>
                                </li>
                            </ul>
                            <button type="button" class="btn btn-default" data-dismiss="modal"><i class="fa fa-times"></i> Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>