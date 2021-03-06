<div class="dokumen"><div class="isilaporan areaprint">
<h2 class="text-center">Neraca per <?php echo $this->session->userdata("periode_sampai") ?></h2><br><br>

<table class='table ' id="ajaxtable">
	<thead>
		<tr>
			<th>Uraian</th>
			<th>Debit</th>
			<th>Kredit</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$this->load->database();
			$dari=$this->session->userdata("periode_dari");
			$sampai=$this->session->userdata("periode_sampai");

			$utama=$this->db->where_in("noakun",array(10000,20000,30000))->get("coa")->result();
			foreach($utama as $t){
			echo "<tr><td colspan=3>$t->noakun-$t->nakun</td></tr>";
			$akun=$this->db
				->select("c.noakun")
				->select("lakun")			
				->select("nakun")
				->where("c.katakun",$t->noakun)->get("coa c");
			$total=0;
			$tdebit=0;
			$tkredit=0;

			foreach($akun->result() as $u){
				$debit=0;
				$kredit=0;
				$balance=$this->db
							->where("tjurnal <=",$sampai)
							->where("tjurnal >=",$dari)
							->where("noakun",$u->noakun)
							->select("sum(debit-kredit) as balance",false)
							->from("jurnal j")
							->join("djurnal d","d.kjurnal=j.kjurnal")
							->get()->row()->balance;

				if($u->noakun==32000){
							$balance+=$this->db
							->where("tjurnal <=",$sampai)
							->where("tjurnal >=",$dari)
							->where("noakun >",40000)
							->select("sum(debit-kredit) as balance",false)
							->from("jurnal j")
							->join("djurnal d","d.kjurnal=j.kjurnal")
							->get()->row()->balance;
				}

				$total+=$balance;

				if($balance<0){
					$kredit+=$balance*-1;
				}else{
					$debit+=$balance;
				}

				echo "<tr><td>";
				switch($u->lakun){
					case "1":
						echo "<span class='nakun lv1'>";
					break;					
					case "2":
						echo "<span class='nakun lv2'> ";
					break;					
					case "3":
						echo "<span class='nakun lv3'> ";
					break;
				}
				echo $u->noakun ."- ". $u->nakun;
				if($u->lakun>1) echo "<td>$debit</td><td>$kredit</td>";
				if($u->lakun<2) echo "<td></td><td></td>";
			}
				if($total<0){
					$tkredit=$total*-1;
				}else{
					$tdebit=$total;
				}


			echo "<tr><td colspan=1><b>Total $t->nakun<br><br></b></td><td><b>$tdebit</b></td><td><b>$tkredit</b></td></tr>";
		}
		?>
	</tbody>
</table>

</div>
<br><br>
<a href="#" id="printnormal" class="btn btn-default">Print</a>
<a href="#" id="printpdf" class="btn btn-default">Print PDF</a>
<a href="<?=base_url()?>akuntansi/laporan" class="btn btn-default">Kembali</a>
</div>
<script src="<?php echo base_url(); ?>asset/js/html2canvas.min.js"></script>
<script src="<?php echo base_url(); ?>asset/js/jspdf.min.js"></script>
<script src="<?php echo base_url(); ?>asset/js/html2pdf.js"></script>
<script>
$(document).ready(function() {
	$("#printnormal").click(function(){
			window.print();
		return false;
	});

	$("#printpdf").click(function(){
			var doc = new jsPDF();

			var html = $(".isilaporan").get(0);
			var option = {
							  margin:       1,
							  filename:     "<?php echo "Laporan Penjualan [".substr(md5(date("Y-m-d H:i:s")),0,8); ?>].pdf",
							  image:        { type: 'jpeg', quality: 0.98 },
							  html2canvas:  { dpi: 400, letterRendering: true },
							  jsPDF:        { unit: 'in', format: 'letter', orientation: 'portrait'}
						};

			html2pdf(html, option);

		return false;
	});
});
</script>