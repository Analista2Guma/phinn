<?php
class Ordenproduccion_controller extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->library('session');
        $user = $this->session->userdata('logged');

        if (!isset($user)) {
            redirect(base_url().'index.php','refresh');
        }
    }

    public function index()
    {
        $data['coordinadores'] = $this->Ordenproduccion_model->ListarCoord();
        $data['listaReport'] = $this->Ordenproduccion_model->listaReportes();
        $data['lista'] = $this->Ordenproduccion_model->Listar();
        $data['turnos'] = $this->Ordenproduccion_model->ListarTurno();
        $this->load->view('header');
        $this->load->view('dashboardclean');
        $this->load->view('Supervisor/orden_produccion', $data);
        $this->load->view('footer');
    }

    public function GuardarRD()
    {
        $cons = $this->input->get_post("cons");
        $NoOrd = $this->input->get_post("noOrden1");
        $turno = $this->input->get_post("turno");
        $FechaInic = $this->input->get_post("Fechainicio");
        $FechaFin = $this->input->get_post("Fechafin");
        $coor = $this->input->get_post("coordinador");
        $grupo = $this->input->get_post("grupo");
        $tipopapel = $this->input->get_post("papel");
        $this->Ordenproduccion_model->Guardar($cons, $NoOrd, $turno, $FechaInic, $FechaFin ,$coor ,$grupo ,$tipopapel);
        redirect("OrdenProduccion");
    }

    public function buscarUltConsc($dias, $numOrden) {
        $this->Ordenproduccion_model->buscarUltC($dias, $numOrden);
    }

    public function agregaDetalleOrdT1($idReporteD) {
        $list = $this->tiemposMuertos_Model->listarTM($idReporteD);
        $array = array();
        $i=0;
        if ($list!=false) {
            foreach($list as $row){
            $horaInicio = date('g:i A', strtotime($row['HoraInicio']));
            $horaFinal = date('g:i A', strtotime($row['HoraFin']));
            $horaMD = new DateTime('00:00:00');
            $datetime1 = new DateTime($row['HoraInicio']);
            $datetime2 = new DateTime($row['HoraFin']);

            if ($datetime2<$datetime1) {
                $time1 = $horaMD->diff($datetime2);
                $time2 = $horaMD->diff($datetime2);
                $tf=$this->sumaRestaHoras($horaFinal,$horaInicio);
                
            }else {
                $interval = $datetime1->diff($datetime2);
                $tf = $interval->format("%H:%I");
            }       
            if ($row['Maquina']==1) {
                $maquina="Maquina 1";
            } else {$maquina="Maquina 2";}

            $array[$i]['IdTiempoMuerto'] = $row['IdTiempoMuerto'];
            $array[$i]['IdReporteDiario'] = $row['IdReporteDiario'];
            $array[$i]['NoOrden'] = $row['NoOrden'];
            $array[$i]['HoraInicio'] = $horaInicio;
            $array[$i]['Turno'] = $row['Turno'];
            $array[$i]['HoraFin'] = $horaFinal;
            $array[$i]['Intervalos'] = $tf;
            $array[$i]['Maquina'] = $maquina;
            $array[$i]['Descripcion'] = $row['Descripcion'];
            $i++;
        }           
        }else {
            $array[$i]['IdTiempoMuerto'] = '-';
            $array[$i]['IdReporteDiario'] = '-';
            $array[$i]['NoOrden'] = '-';
            $array[$i]['HoraInicio'] = '-';
            $array[$i]['Turno'] = '-';
            $array[$i]['HoraFin'] = '-';
            $array[$i]['Intervalos'] = '-';
            $array[$i]['Maquina'] = '-';
            $array[$i]['Descripcion'] = '-';

        } 

        $data['tiemposM'] = $array;
        $data['consecutivo'] = $this->Ordenproduccion_model->buscarRtpDiario($idReporteD);
        //$data['consecutivo'] = array('NoOrden' => $Norden, 'consecutivo' => $consecutivo, 'turno' => $turno);
        $data['listaMaq'] = $this->Ordenproduccion_model->listarMaquinas();
        $this->load->view('header');
        $this->load->view('dashboardclean');
        $this->load->view('Supervisor/detalle_orden_trabajo', $data);
        $this->load->view('footer');
    }

    public function agregaDetalleOrdT($idReporteD) {
        //$data['consecutivo'] = array('NoOrden' => $Norden, 'consecutivo' => $consecutivo, 'turno' => $turno);
        $data['consecutivo'] = $this->Ordenproduccion_model->buscarRtpDiario($idReporteD);
        $this->load->view('header');
        $this->load->view('dashboardclean');
        $this->load->view('Supervisor/menu_orden_trabajo', $data);
        $this->load->view('footer');
    }
    public function ValidarFecha($Fecha,$turno,$Conse)
    {
        $this->Ordenproduccion_model->Valfec($Fecha,$turno,$Conse);
    }
    public function sumaRestaHoras($horainicio, $horafin){
        $dif=date("H.i:s", strtotime("00:00:00") + strtotime($horainicio) - strtotime($horafin) );
        return $dif;
    }
  
}