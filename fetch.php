<?php  
 $connect = mysqli_connect("localhost", "root", "", "super");  
 $output = '';  
 $sql = "SELECT * FROM `table` WHERE description like '%".$_POST["search"]."%'"; 
 $result = mysqli_query($connect, $sql); 
 if(mysqli_num_rows($result) > 0)  
 {  
      $output .= '<h4 align="center">Search Result</h4>';  
      $output .= '<div class="table-responsive">  
                          <table class="table table bordered">  
                               <tr>  
                                    <th>Videos</th>
									<th>Description</th>
                               </tr>
                            ';  
      while($row = mysqli_fetch_array($result))  
      {  
           $output .= '  
                <tr>  
                     <td><div class="thumbnail">
					 <iframe width="253" height="150" src='.$row["1"].' frameborder="0" allowfullscreen></iframe>
					  </div>
					 </td>  
                     <td>'.$row["2"].'</td>
                </tr>
           ';  
      }  
      echo $output;  
 }  
 else  
 {  
      echo 'Data Not Found';  
 }  



 ?>  