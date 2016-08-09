<?php
class SwapModel extends CI_Model 
{
    
    
    public function __construct()
    {
        $this->load->database();
    }
    
    
    public function applySwap($reqRoasterId,$reqShiftId,$reqSwapDate)
    {
        //echo "------------------".$leaveTypeID."----------";
        $data = array(
                'roaster_id_requested' => $reqRoasterId,
                'shift_id_requested' => $reqShiftId,
                
                'roaster_id_accepted' => $reqRoasterId
            );
        $this->db->insert('swap_request',$data);
        $swapId = $this->db->insert_id();
        return $swapId;
    }
    
    public function availableSwap( $swapDate,$shiftId)
    {
        $this->db->select("staff_id,date")
                ->from('roaster')
                
                ->where('date',$swapDate)
                ->where('shift_id !=',$shiftId)
                
                ;
        $query = $this->db->get();
        echo $this->db->last_query();
        if($query->num_rows() > 0)
        {
            echo "has a row";
            $showSwap = $query->result();
            $showswapId = 0;
        }
        else 
        {
            echo "no rows";
            $showswapId = -1;
            $showSwap = null;
        }
        return $showSwap;
    }
    
}
?>