import { ERoles } from "../middleware/interfaces/ERoles";

export default {
	"GET /api/user/roles": {
		handler: "UserController.getAllRoles",
		allowRoles: [ERoles.GLOBAL_ADMIN]
	}
};
