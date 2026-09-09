import { Link } from "react-router-dom";

const StudentTable = ({ students = [], onDelete }) => {
    if (students.length === 0) {
        return (
            <div className="alert alert-info mt-3">
                No students found.
            </div>
        );
    }

    return (
        <div className="card shadow-sm mt-4">
            <div className="card-header">
                <h5 className="mb-0">Registered Students</h5>
            </div>

            <div className="table-responsive">
                <table className="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Registration No.</th>
                            <th>Name</th>
                            <th>Gender</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        {students.map((student, index) => (
                            <tr key={student.id || student.registration_number}>
                                <td>{index + 1}</td>

                                <td>
                                    {student.registration_number}
                                </td>

                                <td>
                                    {student.first_name}{" "}
                                    {student.middle_name}{" "}
                                    {student.last_name}
                                </td>

                                <td>
                                    {student.gender}
                                </td>

                                <td>
                                    {student.email}
                                </td>

                                <td>
                                    {student.phone}
                                </td>

                                <td>
                                    <div className="d-flex gap-2">
                                        <Link
                                            to={`/students/${student.id}`}
                                            className="btn btn-sm btn-info"
                                        >
                                            View
                                        </Link>

                                        <button
                                            type="button"
                                            className="btn btn-sm btn-danger"
                                            onClick={() =>
                                                onDelete &&
                                                onDelete(student.id)
                                            }
                                        >
                                            Delete
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
        </div>
    );
};

export default StudentTable;