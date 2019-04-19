import { ERoles } from "../middleware/interfaces/ERoles";

export default {
	"GET /api/user/roles": {
		handler: "UserController.getAllRoles",
		allowRoles: [ERoles.GLOBAL_ADMIN]
	},
	"GET /api/user": {
		handler: "UserController.getUserInfo",
		allowRoles: [ERoles.ORGANIZER, ERoles.JUDGE, ERoles.LOCAL_ADMIN, ERoles.GLOBAL_ADMIN]
	}
};
