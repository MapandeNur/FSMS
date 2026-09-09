import { useLocation, useParams, Link } from "react-router-dom";

const StudentDetails = () => {
    const { id } = useParams();
    const location = useLocation();

    const student = location.state?.student;

    if (!student) {
        return (
            <div className="container-fluid py-4">
                <div className="alert alert-warning">
                    <h5>Student Not Found</h5>
                    <p className="mb-3">
                        Student information is not available.
                    </p>

                    <Link
                        to="/students"
                        className="btn btn-primary"
                    >
                        Back to Students
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="container-fluid py-4">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Student Details</h2>
                    <p className="text-muted mb-0">
                        View complete student information
                    </p>
                </div>

                <Link
                    to="/students"
                    className="btn btn-secondary"
                >
                    Back to Students
                </Link>
            </div>

            <div className="card shadow-sm">
                <div className="card-header">
                    <h5 className="mb-0">
                        {student.first_name}{" "}
                        {student.middle_name}{" "}
                        {student.last_name}
                    </h5>
                </div>

                <div className="card-body">
                    <div className="row g-3">

                        <div className="col-md-6">
                            <strong>Registration Number</strong>
                            <p className="form-control-plaintext">
                                {student.registration_number}
                            </p>
                        </div>

                        <div className="col-md-6">
                            <strong>Gender</strong>
                            <p className="form-control-plaintext">
                                {student.gender}
                            </p>
                        </div>

                        <div className="col-md-6">
                            <strong>Date of Birth</strong>
                            <p className="form-control-plaintext">
                                {student.date_of_birth}
                            </p>
                        </div>

                        <div className="col-md-6">
                            <strong>Email</strong>
                            <p className="form-control-plaintext">
                                {student.email}
                            </p>
                        </div>

                        <div className="col-md-6">
                            <strong>Phone</strong>
                            <p className="form-control-plaintext">
                                {student.phone}
                            </p>
                        </div>

                        <div className="col-md-6">
                            <strong>Address</strong>
                            <p className="form-control-plaintext">
                                {student.address}
                            </p>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    );
};

export default StudentDetails;