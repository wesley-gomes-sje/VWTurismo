<?php

require_once './Models/Vehicle.php';
require_once './Models/Ticket.php';
require_once './Models/Route.php';
require_once './Models/City.php';
require_once './Views/menuView.php';

class TicketsController
{
    private $ticketModel;
    private $routeModel;
    private $ticketView;
    private $vehicleModel;
    private $cityModel;
    private $passenger;
    public function __construct()
    { 
        
        $this->vehicleModel = new Vehicle();
        $this->ticketView = new menuView();
        $this->ticketModel = new Ticket();
        $this->routeModel = new Route();
        $this->cityModel = new City();
        $this->passenger = $_SESSION['idUser'];
    }
    public function open($message = '', $data=[])
    {
        $cities = $this->cityModel->all();
        $vehicles = $this->vehicleModel->all();
        $content = $this->ticketView;
        $content->registerTicket($message, $cities, $vehicles,$data);
    }
    
    
    public function show(){
        $data = $this-> ticketModel->all();
        $this->ticketView->allTickets($data);

    }

    public function register()
    {
        $vehicle = filter_input(INPUT_POST, 'vehicle', FILTER_VALIDATE_INT);
        $origin = filter_input(INPUT_POST, 'origin', FILTER_VALIDATE_INT);
        $destination = filter_input(INPUT_POST, 'destination', FILTER_VALIDATE_INT);
        $date = $this->sanitizeString($_POST['date'] ?? '');

        if (!$origin || !$destination || !$date) {
            return $this->open('Preencha todos os dados.');
        }
        
        $hasRoute = $this->routeModel->check($origin, $destination);

        if (!$hasRoute) {
            return $this->open("Não existe passagem entre as cidades selecionadas.");
        }
        
        $this->ticketModel->setPassenger($this->passenger);
        $this->ticketModel->setVehicle($vehicle);
        $this->ticketModel->setRoute($hasRoute['id']);
        $this->ticketModel->setDate($date);
        $this->ticketModel->setPrice($this->ticketModel->calculatePrice($hasRoute['distance']));
        
        if (!$this->ticketModel->register()) {
            return $this->open('Erro ao registrar passagem');
        }
        return $this->open('Passagem comprada com sucesso.', $this->all());
        
    }

    public function all(){
        $idPassenger = $this->passenger;
        $tickets =  $this->ticketModel->showTicketsByPassenger($idPassenger);
        return $this->ticketView->all($tickets);
    }
    
    private function sanitizeString(?string $string): string
    {
        return htmlspecialchars(strip_tags($string ?? ''), ENT_QUOTES, 'UTF-8');
    }
}
