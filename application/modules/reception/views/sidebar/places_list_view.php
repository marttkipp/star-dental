<select class="form-control" name="about_us" required="required">
    <option value="">------- SELECT AN OPTION -------- </option>
    <?php
        if($places->num_rows() > 0)
        {
            $places = $places->result();
            
            foreach($places as $res)
            {
                $place_id1 = $res->place_id;
                $place_name = $res->place_name;
                
                if($place_id1 ==  set_value("place_id"))
                {
                    echo '<option value="'.$place_id1.'" selected>'.$place_name.'</option>';
                }
                
                else
                {
                    echo '<option value="'.$place_id1.'">'.$place_name.'</option>';
                }
            }
        }
    ?>

</select>