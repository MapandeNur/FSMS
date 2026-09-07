import { useMemo, useState } from "react";
import StudentForm from "../components/StudentForm";
import StudentTable from "../components/StudentTable";

const Students = () => {
    const [students, setStudents] = useState([]);
    const [search, setSearch] = useState("");
    const [genderFilter, setGenderFilter] = useState("");

    const handleStudentAdded = (student) => {
        const newStudent = {
            ...student,
            id: Date.now(),
        };

        setStudents((prevStudents) => [
            ...prevStudents,
            newStudent,
        ]);
    };

    const handleDelete = (id) => {
        const confirmed = window.confirm(
            "Are you sure you want to delete this student?"
        );

        if (!confirmed) {
            return;
        }

        setStudents((prevStudents) =>
            prevStudents.filter((student) => student.id !== id)
        );
    };

    const filteredStudents = useMemo(() => {
        return students.filter((student) => {
            const fullName = `
                ${student.first_name}
                ${student.middle_name}
                ${student.last_name}
            `.toLowerCase();

            const searchValue = search.toLowerCase();

            const matchesSearch =
                fullName.includes(searchValue) ||
                student.registration_number
                    ?.toLowerCase()
                    .includes(searchValue) ||
                student.email
                    ?.toLowerCase()
                    .includes(searchValue) ||
                student.phone
                    ?.toLowerCase()
                    .includes(searchValue);

            const matchesGender =
                !genderFilter ||
                student.gender === genderFilter;

            return matchesSearch && matchesGender;
        });
    }, [students, search, genderFilter]);

    return (
        <div className="container-fluid py-4">
            <div className="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2>Students</h2>
                    <p className="text-muted mb-0">
                        Register and manage students
                    </p>
                </div>

                <span className="badge bg-primary fs-6">
                    Total: {students.length}
                </span>
            </div>

            {/* Registration Form */}
            <StudentForm
                onStudentAdded={handleStudentAdded}
            />

            {/* Search and Filter */}
            <div className="card shadow-sm mt-4">
                <div className="card-body">
                    <div className="row g-3">

                        <div className="col-md-8">
                            <label className="form-label">
                                Search Students
                            </label>

                            <input
                                type="text"
                                className="form-control"
                                placeholder="Search by name, registration number, email or phone..."
                                value={search}
                                onChange={(e) =>
                                    setSearch(e.target.value)
                                }
                            />
                        </div>

                        <div className="col-md-4">
                            <label className="form-label">
                                Filter by Gender
                            </label>

                            <select
                                className="form-select"
                                value={genderFilter}
                                onChange={(e) =>
                                    setGenderFilter(e.target.value)
                                }
                            >
                                <option value="">
                                    All Genders
                                </option>

                                <option value="Male">
                                    Male
                                </option>

                                <option value="Female">
                                    Female
                                </option>
                            </select>
                        </div>

                    </div>
                </div>
            </div>

            {/* Students Table */}
            <StudentTable
                students={filteredStudents}
                onDelete={handleDelete}
            />
        </div>
    );
};

export default Students;