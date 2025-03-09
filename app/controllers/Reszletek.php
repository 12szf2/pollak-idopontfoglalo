<?php
#[AllowDynamicProperties]

class Reszletek extends Controller
{
    public function __construct()
    {
        $this->reszletekModel = $this->model('ReszletekModel');
    }

    // Az esemény részleteinek megjelenítése
    public function index($id)
    {

        $data = [
            'reszletek' => $this->reszletekModel->egyAdottEsemenyReszletei($id)
        ];

        $this->view('reszletek/index', $data);

        //Hozzáadás
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_SPECIAL_CHARS, FILTER_FLAG_NO_ENCODE_QUOTES);

            $esemenyID = (int)trim($_POST['esemenyID']);
            $email = trim($_POST['email']);
            $neve = trim($_POST['neve']);

            $msg = $this->reszletekModel->emailHozzadas(
                $esemenyID,
                $email,
                $neve
            );

            header('location:' . URLROOT . '/reszletek/' . $esemenyID . '?msg=' . $msg);
        }
    }

    public function jelentkezes($id)
    {

        $this->view('reszletek/jelentkezes/index');

        $esemenyID = $this->reszletekModel->visszaigazol($id);

        if ($esemenyID) {
            header('location:' . URLROOT . '/reszletek/' . $esemenyID . '?msg=2');
        } else {
            header('location:' . URLROOT . '/reszletek/' . $esemenyID . '?msg=3');
        }
    }

    public function jelentkezesTorles($id)
    {

        $this->view('reszletek/jelentkezesTorles/index');

        $esemenyID = $this->reszletekModel->torol($id);

        if ($esemenyID) {
            header('location:' . URLROOT);
        } else {
            header('location:' . URLROOT);
        }
    }
}
