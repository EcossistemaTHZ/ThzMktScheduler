import { zodResolver } from '@hookform/resolvers/zod';
import { Delete, Edit, PersonAdd, Save } from '@mui/icons-material';
import {
  Alert,
  Box,
  Button,
  Card,
  CardContent,
  CardHeader,
  CircularProgress,
  IconButton,
  Table,
  TableBody,
  TableCell,
  TableContainer,
  TableHead,
  TableRow,
  TextField,
  Typography,
} from '@mui/material';
import { useMutation, useQuery, useQueryClient } from '@tanstack/react-query';
import { useEffect, useState } from 'react';
import { useForm } from 'react-hook-form';
import { useTranslation } from 'react-i18next';
import { z } from 'zod';
import { createUser, deleteUser, getUsers, updateUser } from '../lib/api';
import type { User, UserInput } from '../lib/types';
import { ConfirmDialog } from './ConfirmDialog';
import { useFeedback } from './FeedbackProvider';

type UserFormValues = UserInput;

const EMPTY_VALUES: UserFormValues = { name: '', email: '' };

export function UserManager() {
  const { t } = useTranslation();
  const queryClient = useQueryClient();
  const { showSuccess, showError } = useFeedback();
  const [editing, setEditing] = useState<User | null>(null);
  const [deleteTarget, setDeleteTarget] = useState<User | null>(null);

  const {
    data: users,
    isLoading,
    isError,
  } = useQuery({
    queryKey: ['users'],
    queryFn: getUsers,
  });

  const schema = z.object({
    name: z.string().min(1, { message: t('COMMON.REQUIRED') }),
    email: z.email({ message: t('COMMON.INVALID_EMAIL') }),
  });

  const {
    register,
    handleSubmit,
    reset,
    formState: { errors },
  } = useForm<UserFormValues>({
    resolver: zodResolver(schema),
    defaultValues: EMPTY_VALUES,
  });

  useEffect(() => {
    reset(editing ? { name: editing.name, email: editing.email } : EMPTY_VALUES);
    if (editing) {
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }, [editing, reset]);

  const saveMutation = useMutation({
    mutationFn: (values: UserFormValues) =>
      editing?.id ? updateUser(editing.id, values) : createUser(values),
    onSuccess: () => {
      showSuccess(t(editing ? 'SUCCESS.USER_UPDATE' : 'SUCCESS.USER_CREATE'));
      queryClient.invalidateQueries({ queryKey: ['users'] });
      reset(EMPTY_VALUES);
      setEditing(null);
    },
    onError: () => {
      showError(t(editing ? 'ERRORS.USER_UPDATE' : 'ERRORS.USER_CREATE'));
    },
  });

  const deleteMutation = useMutation({
    mutationFn: (id: number) => deleteUser(id),
    onSuccess: () => {
      showSuccess(t('SUCCESS.USER_DELETE'));
      queryClient.invalidateQueries({ queryKey: ['users'] });
      setDeleteTarget(null);
    },
    onError: () => {
      showError(t('ERRORS.USER_DELETE'));
      setDeleteTarget(null);
    },
  });

  const onSubmit = handleSubmit((values) => saveMutation.mutate(values));

  return (
    <Box sx={{ display: 'flex', flexDirection: 'column', gap: 3 }}>
      <Card sx={{ borderLeft: '4px solid #ff4081' }}>
        <CardHeader title={t(editing ? 'USER_MANAGER.TITLE_EDIT' : 'USER_MANAGER.TITLE_ADD')} />
        <CardContent>
          <form onSubmit={onSubmit} noValidate>
            <Box sx={{ display: 'flex', gap: 2, alignItems: 'flex-start', flexWrap: 'wrap' }}>
              <TextField
                label={t('USER_MANAGER.NAME')}
                placeholder={t('USER_MANAGER.FULL_NAME')}
                required
                sx={{ flex: 1, minWidth: 220 }}
                error={Boolean(errors.name)}
                helperText={errors.name?.message}
                {...register('name')}
              />
              <TextField
                label={t('USER_MANAGER.EMAIL')}
                placeholder={t('USER_MANAGER.EMAIL_ADDRESS')}
                type="email"
                required
                sx={{ flex: 1, minWidth: 220 }}
                error={Boolean(errors.email)}
                helperText={errors.email?.message}
                {...register('email')}
              />
              <Button
                type="submit"
                variant="contained"
                color="secondary"
                disabled={saveMutation.isPending}
                sx={{ height: 56, mb: 2 }}
                startIcon={editing ? <Save /> : <PersonAdd />}
              >
                {t(editing ? 'COMMON.SAVE' : 'USER_MANAGER.ADD')}
              </Button>
              {editing && (
                <Button
                  type="button"
                  variant="text"
                  color="error"
                  onClick={() => setEditing(null)}
                  sx={{ height: 56, mb: 2 }}
                >
                  {t('COMMON.CANCEL')}
                </Button>
              )}
            </Box>
          </form>
        </CardContent>
      </Card>

      <Card>
        <CardHeader title={t('USER_MANAGER.TITLE_LIST')} />
        <CardContent>
          {isLoading && (
            <Box sx={{ display: 'flex', justifyContent: 'center', py: 6 }}>
              <CircularProgress />
            </Box>
          )}

          {isError && <Alert severity="error">{t('ERRORS.LOAD_DATA')}</Alert>}

          {!isLoading && !isError && (
            <TableContainer>
              <Table size="small">
                <TableHead>
                  <TableRow>
                    <TableCell>{t('USER_MANAGER.NAME')}</TableCell>
                    <TableCell>{t('USER_MANAGER.EMAIL')}</TableCell>
                    <TableCell align="right">{t('COMMON.ACTIONS')}</TableCell>
                  </TableRow>
                </TableHead>
                <TableBody>
                  {users && users.length > 0 ? (
                    users.map((user) => (
                      <TableRow key={user.id}>
                        <TableCell>{user.name}</TableCell>
                        <TableCell>{user.email}</TableCell>
                        <TableCell align="right">
                          <IconButton
                            size="small"
                            color="primary"
                            title={t('COMMON.EDIT')}
                            onClick={() => setEditing(user)}
                          >
                            <Edit />
                          </IconButton>
                          <IconButton
                            size="small"
                            color="error"
                            title={t('COMMON.DELETE')}
                            onClick={() => setDeleteTarget(user)}
                          >
                            <Delete />
                          </IconButton>
                        </TableCell>
                      </TableRow>
                    ))
                  ) : (
                    <TableRow>
                      <TableCell colSpan={3} sx={{ textAlign: 'center', color: '#666', py: 6 }}>
                        <Typography variant="body1">{t('USER_MANAGER.NO_USERS')}</Typography>
                      </TableCell>
                    </TableRow>
                  )}
                </TableBody>
              </Table>
            </TableContainer>
          )}

          <ConfirmDialog
            open={Boolean(deleteTarget)}
            title={t('COMMON.CONFIRM_DELETE')}
            message={deleteTarget ? `${deleteTarget.name} (${deleteTarget.email})` : ''}
            confirmLabel={t('COMMON.DELETE')}
            cancelLabel={t('COMMON.CANCEL')}
            onConfirm={() => deleteTarget?.id !== undefined && deleteMutation.mutate(deleteTarget.id)}
            onCancel={() => setDeleteTarget(null)}
          />
        </CardContent>
      </Card>
    </Box>
  );
}