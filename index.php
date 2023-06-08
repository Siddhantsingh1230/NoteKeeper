<?php
//ALL Globals 
$isinserted=false;
$isUpdated=false;
$isNotInserted=false;
$isDeleted=false;
$idCount=0;
//Database Connectivity
$server="localhost";
$username="root";
$password="";
$db="siddb";
//creating connection object
$conn=mysqli_connect($server,$username,$password,$db); 
if(!$conn){
  die("connection failed".mysqli_connect_error());
}

if($_SERVER['REQUEST_METHOD']=="POST"){
    if(isset($_POST['title'])&& isset($_POST['desc'])){
        $title=$_POST['title'];
        $desc=$_POST['desc'];
        if($title!="" && $desc!="" ){
            $insertQuery="insert into notes(title,description) values('$title','$desc')";
            $result=mysqli_query($conn,$insertQuery);
            if($result){
                $isinserted=true;
            }
        }else{
            $isNotInserted=true;
        }
    }
    //for updating the notes
    if(isset($_POST['id'])){
    $updateTitle=$_POST['titleEdit'];
    $updateDesc=$_POST['descEdit'];
    $updateId=$_POST['id'];
     $updateQuery="update notes set title='$updateTitle',description='$updateDesc' where id=$updateId;";
     $result=mysqli_query($conn,$updateQuery);
     if($result){
        $isUpdated=true;
     }
    }
    if(isset($_POST['deleteId'])){
        $ID=$_POST['deleteId'];
        $deleteQuery="delete from notes where id=$ID;";
        $result=mysqli_query($conn,$deleteQuery);
        if($result){
            $isDeleted=true;
        }
    }
 
    
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>NOTES APP PHP</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous" />
    <link rel="stylesheet" href="//cdn.datatables.net/1.13.4/css/jquery.dataTables.min.css">    
</head>

<body>
    <!-- Hidden Form For Delete Opeartion -->
    <form action="index.php" hidden id="deleteForm" method="POST">
        <input type="hidden" id="deleteId" name="deleteId">
    </form>
    <!-- Modal -->
    <div class="modal fade" id="Modal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
        <div class="modal-header">
            <h1 class="modal-title fs-5" id="exampleModalLabel">Edit Note</h1>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="editModalForm" action="index.php"  method="POST">
                <input type="hidden" id="hiddenField" name="id">
                <div class="mb-3">
                    <label for="title" class="form-label">Note Title</label>
                    <input required type="text" class="form-control" id="titleEdit" name="titleEdit" aria-describedby="emailHelp" />
                </div>
                <div class="form-floating">
                    <textarea required class="form-control" placeholder="Leave a comment here" id="descEdit" name="descEdit"
                        style="height: 100px"></textarea>
                    <label for="desc">Note Description</label>
                </div>
                
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button id="modalSubmitBtn" type="button"  class="btn btn-primary">Save changes</button>
        </div>
        </div>
    </div>
    </div>
    
    
    <?php
    if($isinserted){
        echo "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Note inserted successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    if($isNotInserted){
        echo "
        <div class='alert alert-danger alert-dismissible fade show' role='alert'>
        <strong>Error!</strong> Note not inserted 
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    if($isUpdated){
        echo "
        <div class='alert alert-primary alert-dismissible fade show' role='alert'>
        <strong>Updated!</strong> Changes made successfuly
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    if($isDeleted){
        echo "
        <div class='alert alert-success alert-dismissible fade show' role='alert'>
        <strong>Success!</strong> Note deleted successfully
        <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
        </div>
        ";
    }
    ?>
    <div class="container my-2 d-flex justify-content-center">
        <div>
            <h1>N O T E S</h1>
            <hr  style="border: 1.5px solid black; background-color: black;">
        </div>
    </div>
    <div class="container my-5">
        <h2 class="my-3">| SaveNotes</h2>
        <form action="index.php"  method="POST">
            <div class="mb-3">
                <label for="title" class="form-label">Note Title</label>
                <input type="text" class="form-control" id="title" name="title" aria-describedby="emailHelp" />
            </div>
            <div class="form-floating">
                <textarea class="form-control" placeholder="Leave a comment here" id="desc" name="desc"
                    style="height: 100px"></textarea>
                <label for="desc">Note Description</label>
            </div>
            <button type="submit" class="btn btn-primary my-3">Add Note</button>
        </form>
        <hr>
    </div>
    
    <div class="container">
    
        <table class="table" id="myTable">
            <thead>
                <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    $select="select * from notes";
                    $result=mysqli_query($conn,$select);
                    
                    while ($row=mysqli_fetch_assoc($result)) {
                      echo ("
                      <tr>
                        <th scope='col'>".++$idCount."</th>
                        <th scope='col'>".$row['title']."</th>
                        <th scope='col'>".$row['description']."</th>
                        <th scope='col'><button id=".$row['id']." data-bs-target='#Modal' data-bs-toggle='modal' class='edit btn btn-sm btn-primary mx-2 my-1'>Edit</button> <button id=".'id'.$row['id']." class='delete btn btn-sm btn-danger'>Delete</button></th>
                      </tr> 
                      ");
                    }
                ?>
            </tbody>
        </table>
    </div>
    <div class="container-fluid d-flex justify-content-center my-3">
        <p>Created By <span style="font-size: small;">❤️</span>  Siddhant Singh </p>
    </div>

    <!--- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <script src="//cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>

    <!-- Edit Btn / Delete Btn / Edit Modal Configuration and dataTable Integration -->
    <script>
        let editBtns=document.getElementsByClassName('edit');
        Array.from(editBtns).forEach((editBtn)=>{
            editBtn.addEventListener('click',(e)=>{
                tr=e.target.parentNode.parentNode;
                title=tr.getElementsByTagName("th")[1].innerText;
                desc=tr.getElementsByTagName("th")[2].innerText;
                titleEdit.value=title;
                descEdit.value=desc;
                hiddenField.value=e.target.id;
                console.log(hiddenField.value);

            })
        });
        //Modal Submit Button
        modalSubmitBtn.addEventListener('click',()=>{
            editModalForm.submit();
        });
        //Delete Btns Configurations
        let deleteBtns=document.getElementsByClassName('delete');
        Array.from(deleteBtns).forEach((deleteBtn)=>{
            deleteBtn.addEventListener('click',(e)=>{      
                if(confirm("Do you really want to delete!!")){
                    Id=e.target.id.substring(2);
                    deleteId.value=Id;
                    deleteForm.submit();
                }
            })
        });



        let table = new DataTable('#myTable');
    </script>   
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
        integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"
        integrity="sha384-fbbOQedDUMZZ5KreZpsbe1LCZPVmfTnH7ois6mU1QK+m14rQ1l2bGBq41eYeM/fS" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz" crossorigin="anonymous">
    </script>
</body>

</html>