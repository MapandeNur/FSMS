import { useState } from "react";

const StudentForm = ({ onStudentAdded }) => {
    const [formData, setFormData] = useState({
        registration_number: "",
        first_name: "",
        middle_name: "",
        last_name: "",
        gender: "",
        date_of_birth: "",
        email: "",
        phone: "",
        address: "",
    });

    const [errors, setErrors] = useState({});

    const handleChange = (e) => {
        const { name, value } = e.target;

        setFormData((prev) => ({
            ...prev,
            [name]: value,
        }));

        // Remove error when user starts correcting the field
        if (errors[name]) {
            setErrors((prev) => ({
                ...prev,
                [name]: "",
            }));
        }
    };

    const validate = () => {
        const newErrors = {};

        if (!formData.registration_number.trim()) {
            newErrors.registration_number = "Registration number is required";
        }

        if (!formData.first_name.trim()) {
            newErrors.first_name = "First name is required";
        }

        if (!formData.last_name.trim()) {
            newErrors.last_name = "Last name is required";
        }

        if (!formData.gender) {
            newErrors.gender = "Gender is required";
        }

        if (!formData.date_of_birth) {
            newErrors.date_of_birth = "Date of birth is required";
        }

        if (!formData.email.trim()) {
            newErrors.email = "Email is required";
        } else if (!/\S+@\S+\.\S+/.test(formData.email)) {
            newErrors.email = "Enter a valid email address";
        }

        if (!formData.phone.trim()) {
            newErrors.phone = "Phone number is required";
        }

        setErrors(newErrors);

        return Object.keys(newErrors).length === 0;
    };

    const handleSubmit = (e) => {
        e.preventDefault();

        if (!validate()) {
            return;
        }

        if (onStudentAdded) {
            onStudentAdded(formData);
        }

        // Reset form
        setFormData({
            registration_number: "",
            first_name: "",
            middle_name: "",
            last_name: "",
            gender: "",
            date_of_birth: "",
            email: "",
            phone: "",
            address: "",
        });

        setErrors({});
    };

    return (
        <div className="card shadow-sm">
            <div className="card-header">
                <h5 className="mb-0">Student Registration</h5>
            </div>

            <div className="card-body">
                <form onSubmit={handleSubmit}>
                    <div className="row">

                        {/* Registration Number */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Registration Number
                            </label>

                            <input
                                type="text"
                                name="registration_number"
                                className={`form-control ${
                                    errors.registration_number
                                        ? "is-invalid"
                                        : ""
                                }`}
                                value={formData.registration_number}
                                onChange={handleChange}
                                placeholder="Enter registration number"
                            />

                            {errors.registration_number && (
                                <div className="invalid-feedback">
                                    {errors.registration_number}
                                </div>
                            )}
                        </div>

                        {/* First Name */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                First Name
                            </label>

                            <input
                                type="text"
                                name="first_name"
                                className={`form-control ${
                                    errors.first_name ? "is-invalid" : ""
                                }`}
                                value={formData.first_name}
                                onChange={handleChange}
                                placeholder="Enter first name"
                            />

                            {errors.first_name && (
                                <div className="invalid-feedback">
                                    {errors.first_name}
                                </div>
                            )}
                        </div>

                        {/* Middle Name */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Middle Name
                            </label>

                            <input
                                type="text"
                                name="middle_name"
                                className="form-control"
                                value={formData.middle_name}
                                onChange={handleChange}
                                placeholder="Enter middle name"
                            />
                        </div>

                        {/* Last Name */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Last Name
                            </label>

                            <input
                                type="text"
                                name="last_name"
                                className={`form-control ${
                                    errors.last_name ? "is-invalid" : ""
                                }`}
                                value={formData.last_name}
                                onChange={handleChange}
                                placeholder="Enter last name"
                            />

                            {errors.last_name && (
                                <div className="invalid-feedback">
                                    {errors.last_name}
                                </div>
                            )}
                        </div>

                        {/* Gender */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Gender
                            </label>

                            <select
                                name="gender"
                                className={`form-select ${
                                    errors.gender ? "is-invalid" : ""
                                }`}
                                value={formData.gender}
                                onChange={handleChange}
                            >
                                <option value="">Select gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>

                            {errors.gender && (
                                <div className="invalid-feedback">
                                    {errors.gender}
                                </div>
                            )}
                        </div>

                        {/* Date of Birth */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Date of Birth
                            </label>

                            <input
                                type="date"
                                name="date_of_birth"
                                className={`form-control ${
                                    errors.date_of_birth
                                        ? "is-invalid"
                                        : ""
                                }`}
                                value={formData.date_of_birth}
                                onChange={handleChange}
                            />

                            {errors.date_of_birth && (
                                <div className="invalid-feedback">
                                    {errors.date_of_birth}
                                </div>
                            )}
                        </div>

                        {/* Email */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                className={`form-control ${
                                    errors.email ? "is-invalid" : ""
                                }`}
                                value={formData.email}
                                onChange={handleChange}
                                placeholder="student@example.com"
                            />

                            {errors.email && (
                                <div className="invalid-feedback">
                                    {errors.email}
                                </div>
                            )}
                        </div>

                        {/* Phone */}
                        <div className="col-md-6 mb-3">
                            <label className="form-label">
                                Phone Number
                            </label>

                            <input
                                type="text"
                                name="phone"
                                className={`form-control ${
                                    errors.phone ? "is-invalid" : ""
                                }`}
                                value={formData.phone}
                                onChange={handleChange}
                                placeholder="Enter phone number"
                            />

                            {errors.phone && (
                                <div className="invalid-feedback">
                                    {errors.phone}
                                </div>
                            )}
                        </div>

                        {/* Address */}
                        <div className="col-12 mb-3">
                            <label className="form-label">
                                Address
                            </label>

                            <textarea
                                name="address"
                                className="form-control"
                                rows="3"
                                value={formData.address}
                                onChange={handleChange}
                                placeholder="Enter student address"
                            />
                        </div>
                    </div>

                    <button type="submit" className="btn btn-primary">
                        Register Student
                    </button>
                </form>
            </div>
        </div>
    );
};

export default StudentForm;