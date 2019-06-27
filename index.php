<?php
require_once 'vendor/autoload.php';
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Blob\Models\ListBlobsOptions;
use MicrosoftAzure\Storage\Blob\Models\CreateContainerOptions;
use MicrosoftAzure\Storage\Blob\Models\PublicAccessType;
use MicrosoftAzure\Storage\Blob\Models\DeleteBlobOptions;
$connectionString = "DefaultEndpointsProtocol=https;AccountName=penyimpananyusuf;AccountKey=atnaaelnl8Ttqw6jXjIkPmUjWHb26Llh4jXlC+59Yc8PGdOLZ6ACpFQUp63zcZzYtsO9SKA/ffBldXON7Hfa8g==;EndpointSuffix=core.windows.net";
$blobClient = BlobRestProxy::createBlobService($connectionString);
$containerName = "penyimpanancomputervision";

	// Kode Untuk upload ke Azure
if (isset($_POST['submit'])) {
	$fileToUpload = $_FILES["fileToUpload"]["name"];
	$content = fopen($_FILES["fileToUpload"]["tmp_name"], "r");
	echo "----------File BlockBlob telah terupload----------".PHP_EOL;
		
	$blobClient->createBlockBlob($containerName, $fileToUpload, $content);
}	
	// Kode Untuk menampilkan daftar file di azure blockblob
	$listBlobsOptions = new ListBlobsOptions();
	$listBlobsOptions->setPrefix("");
	$result = $blobClient->listBlobs($containerName, $listBlobsOptions);

?>
<!DOCTYPE html>
<html>
<head>
<title>Tugas kedua azure blockblob dan azure vision</title>

<head>
<body>
		<h1>AZURE PENYIMPANAN BLOCKBLOB</h1>
		<h3>1. Silahkan Upload File terlebih dahulu untuk menambah gambar ke Azure Blockblob</h3>
		<form method="post" enctype="multipart/form-data">
			<input type="file" name="fileToUpload" accept=".jpeg,.jpg,.png" required="">
			<input type="submit" name="submit" value="Unggah ke Azure blockblob">
		</form>
		<br>
		<table>
			<tr>
				<th>***Nama File***</th>
				<th>***Halaman web dari azure blockblob***</th>
				<th>***Hapus***</th>
			</tr>
			<tbody>
				<?php
					do {
						foreach ($result->getBlobs() as $blob)  
						{
				?>						
			<tr>
				<td><?php echo $blob->getName() ?></td>
				<td>
				<input type="text" value="<?php echo $blob->getUrl() ?>" id="pilih" />
        			<button type="button" onclick="copy_text()">Copy</button>
				<div>
				<button onclick="copy_text()"></button>
				</div>
				</td>
				<td>
				<center><button type="button" onclick="alert('Maaf ! Hapus file Proses pengembangan')">Hapus</button></center>
				</td>
			</tr>
				<?php
						} $listBlobsOptions->setContinuationToken($result->getContinuationToken());
					} while($result->getContinuationToken());
				?>
			</tbody>	
		</table>
		
		<!-- Analisa Gambar dengan Azure Vision Ajax dan menangani CROSS ORIGIN-->
		<script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
		<script>window.jQuery || document.write('<script src="../../assets/js/vendor/jquery-slim.min.js"><\/script>')</script>
		<script src="/jquery.min.js"></script>
		<script src="/jquery-2.1.1.js"></script>
		<br>
		
		<h1>AZURE ANALISA KOMPUTER VISION</h1>
		<h3>2. Kemudian copikan salah satu halaman web gambar diatas di bawah sini !</h3>
		<script type="text/javascript">
		
    		
    		function copy_text() {
        	document.getElementById("pilih").select();
        	document.execCommand("copy");
        	alert("Text berhasil dicopy");
    		}

		function processImage() {
			// **********************************************
			// *** Update or verify the following values. ***
			// **********************************************
		 
			// Replace <Subscription Key> with your valid subscription key.
			var subscriptionKey = "2d5cd30a58e645ff95b8e0ddfbf75f67";
		 
			// You must use the same Azure region in your REST API method as you used to
			// get your subscription keys. For example, if you got your subscription keys
			// from the West US region, replace "westcentralus" in the URL
			// below with "westus".
			//
			// Free trial subscription keys are generated in the "westus" region.
			// If you use a free trial subscription key, you shouldn't need to change
			// this region.
			var uriBase = "https://southeastasia.api.cognitive.microsoft.com/vision/v2.0/analyze";
		 
			// Request parameters.
			var params = {
				"visualFeatures": "Categories,Description,Color",
				"details": "",
				"language": "en",
			};
		 
			// Display the image.
			var sourceImageUrl = document.getElementById("inputImage").value;
			document.querySelector("#sourceImage").src = sourceImageUrl;
		 
			// Make the REST API call.
			$.ajax({
				url: uriBase + "?" + $.param(params),
		 
			// Request headers.
				beforeSend: function(xhrObj){
					xhrObj.setRequestHeader("Content-Type","application/json");
					xhrObj.setRequestHeader(
						"Ocp-Apim-Subscription-Key", subscriptionKey);
				},
		 
				type: "POST",
		 
				// Request body.
				data: '{"url": ' + '"' + sourceImageUrl + '"}',
			})
		 
			.done(function(data) {
				// Show formatted JSON on webpage.
				$("#responseTextArea").val(JSON.stringify(data, null, 2));
			})
		 
			.fail(function(jqXHR, textStatus, errorThrown) {
				// Display error message.
				var errorString = (errorThrown === "") ? "Error. " :
					errorThrown + " (" + jqXHR.status + "): ";
				errorString += (jqXHR.responseText === "") ? "" :
					jQuery.parseJSON(jqXHR.responseText).message;
				alert(errorString);
			});
		};
		</script>
		<br>
		<input type="text" name="inputImage" id="inputImage" style="width:360px;"
			value="http://upload.wikimedia.org/wikipedia/commons/3/3c/Shaki_waterfall.jpg"/>
			<button onclick="processImage()">Tekan untuk menganalisa gambar</button>
		<br><br>
		<div id="wrapper" style="width:1020px; display:table;">
		<div id="jsonOutput" style="width:600px; display:table-cell;">
			Hasil respon:
			<br><br>
		<textarea id="responseTextArea" class="UIInput"
			style="width:580px; height:400px;" readonly="">
		</textarea>
		</div>
		<div id="imageDiv" style="width:420px; display:table-cell;">
			Gambar:
			<br><br>
			<img id="sourceImage" width="400" /><br>
			
		</div>
		</div>
</body>
</html>
