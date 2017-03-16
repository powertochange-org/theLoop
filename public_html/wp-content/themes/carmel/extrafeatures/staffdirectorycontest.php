<div id="staffdirectorycontest">
    <div class="staffcontainer">
        <div class="staffleft">
        <?php
        global $wpdb;
        $sql = "SELECT photo, user_login
                FROM employee
                WHERE share_photo = '1' AND photo IS NOT NULL AND user_login = '$current_user->user_login'
                ";

        $result = $wpdb->get_results($sql, ARRAY_A);

        if($result) {
            $result[0]['photo'];
            $result[0]['user_login'];
            echo '<img src="//'.$_SERVER['SERVER_NAME'].'/wp-content/uploads/staff_photos/'.$result[0]['photo'].'" height="50px"/>';
        } else {
            
            echo '<img src="//'.$_SERVER['SERVER_NAME'].'/wp-content/uploads/staff_photos/anonymous.jpg" height="50px"/>';
        }


        ?>
        </div>
        <div class="staffright">
            <p><a href="/staff-directory/?page=myprofile">Upload or Update</a> your photo to the Loop for a chance to win a Visa Gift Card.
            <br>* Any photos uploaded in 2017 will be automatically entered to win</p>
        </div>
    </div>
</div>

<style>
#staffdirectorycontest {
    background-color: #F58220;
    height:50px;
    padding-top: 10px;
    padding-bottom: 10px;
}
.staffcontainer {
    width: 700px;
    margin-right: auto;
    margin-left: auto;
}
.staffleft {
    width: 100px;
    float: left;
}
.staffright {
    width: 600px;
    float:left;
    font-weight: 500;
}
@media (max-width: 767px) {
    .staffcontainer {
        width: 100%;
    }
    #staffdirectorycontest {
        height: 100px;
    }
    .staffleft {
        width: 25%;
    }
    .staffright {
        width: 65%;
    }
    .staffright p {
        font-size: 12px;
        line-height: normal;
    }
    #mobile-menu {
        top: 350px;
    }
    .menu_bg {
        top: 400px;
    }
}
</style>